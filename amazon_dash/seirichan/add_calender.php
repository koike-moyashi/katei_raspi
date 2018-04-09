<?php
require __DIR__ . '/vendor/autoload.php';

define('APPLICATION_NAME', 'Google Calendar ADD seiri');
define('CREDENTIALS_PATH', __DIR__ . '/add_seiri.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client.json');
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR)
));

if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfigFile(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = file_get_contents($credentialsPath);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));

    // Exchange authorization code for an access token.
    $accessToken = $client->authenticate($authCode);

    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, $accessToken);
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->refreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, $client->getAccessToken());
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
  }
  return str_replace('~', realpath($homeDirectory), $path);
}

/**
 * ----------------------------------------------------------
 * getCurrentDate()
 * 現在の年月日を取得する
 * ----------------------------------------------------------
 */
function getCurrentDate() {
  $dt = new DateTime();
  $dt->setTimeZone(new DateTimeZone('Asia/Tokyo'));
 
  return $dt->format('Y-m-d');
}

function getCurrentTime() {
  $dt = new DateTime();
  $dt->setTimeZone(new DateTimeZone('Asia/Tokyo'));

  return $dt->format('Y-m-d\TH:i:s');
}



// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);

// 取得内容
$calendarId1 = 'ここにカレンダーIDを入れる@group.calendar.google.com';
$optParams = array(
  'maxResults' => 20,
  'orderBy' => 'startTime',
  'timeMin' => date('c', strtotime('-5 week')),
  'singleEvents' => TRUE,
);

// 予定の取得
$results = $service->events->listEvents($calendarId1, $optParams);


if (count($results->getItems()) == 0) {
  print "5週以内のデータが見つかりませんでした\n";
  $past_data=array();
  $between=1;

} else {
  $items = $results->getItems();
  $lastdate = array();
  $deleteid = NULL;

  foreach($items as $item){
    $text = $item->summary;
    //予定を消す
    if ($text == "🌝(予定)"){
      $deleteid = $item->id;
    }

    //前回の日時
    if (preg_match('/🌝\(\d.*日間\)/',$text)){
      if (empty($item->start->dateTime)) {
         array_push($lastdate,$item->start->date);
      } else {
         array_push($lastdate,$item->start->dateTime);
      }
      // descriptionから過去データを抜き出す
      if (!is_null($item->description)){
        $old_day = $item->description;
        $past_data = unserialize($old_day);
      } else {
        $past_data = array();
      }
    }
  }
  $lastdate = end($lastdate);

  // 期間の計算
  $day = ceil( time() - strtotime((string) $lastdate)) / (60 * 60 * 24);
  $between = intval($day);
  $hour =  ceil( time() - strtotime((string) $lastdate)) / (60 * 60);
  // ２重登録防止用
  $between_hr = intval($hour);
  echo "DEBUG:$between_hr:" . $between_hr;

  // 予定日の削除
  if(!is_null($deleteid)){
    $service->events->delete($calendarId1, $deleteid);
    echo "予定を消しました\n";
  }
}


// 今日
// 前回のデータが無かったら今回のデータのみ入れる
if(is_null($past_data)){
  $past_data=array($between);
}else{
  //前回データがあったら配列に加える
  array_push($past_data,$between);
}

$add_time_today = strtotime(getCurrentTime() . "+09:00");
$event_today = new Google_Service_Calendar_Event(array(
  'summary' => '🌝('.$between.'日間)',
  'description' => serialize($past_data),
  'start' => array(
    'dateTime' =>  date('c', $add_time_today),
    'timeZone' => 'Asia/Tokyo',
  ),
  'end' => array(
    'dateTime' => date('c', $add_time_today),
    'timeZone' => 'Asia/Tokyo',
  ),
));

// 次回予定
if(!empty($past_data)){
  // 最近の平均値
  $average = intval(array_sum($past_data)/count($past_data));
  $add_time_next = strtotime(getCurrentTime() . "+09:00") + ($average * 24 * 60 * 60);
  $event_next = new Google_Service_Calendar_Event(array(
    'summary' => '🌝(予定)',
    'start' => array(
      'dateTime' =>  date('c', $add_time_next),
      'timeZone' => 'Asia/Tokyo',
    ),
    'end' => array(
      'dateTime' => date('c', $add_time_next),
      'timeZone' => 'Asia/Tokyo',
    ),
  ));

  // 排卵日予定
  $add_time_hairan = strtotime(getCurrentTime() . "+09:00") + (($average -14 ) * 24 * 60 * 60);
  $event_hairan = new Google_Service_Calendar_Event(array(
    'summary' => '💣',
    'start' => array(
      'dateTime' =>  date('c', $add_time_hairan),
      'timeZone' => 'Asia/Tokyo',
    ),
    'end' => array(
      'dateTime' => date('c', $add_time_hairan),
      'timeZone' => 'Asia/Tokyo',
    ),
  ));
}

// カレンダー登録
// 3時間以内だと登録しない
if ($between_hr > 3) {
  // カレンダーへの登録
  if(!empty($event_today)){
    $service->events->insert($calendarId1, $event_today);
    echo "今日を登録しました\n";
  }
  if(!empty($event_next)){
    $service->events->insert($calendarId1, $event_next);
    echo "予定を登録しました\n";
  }
  if(!empty($event_hairan)){
    $service->events->insert($calendarId1, $event_hairan);
    echo "排卵日を登録しました\n";
  }
} else {
  echo "既に登録されています\n";
}

?>
