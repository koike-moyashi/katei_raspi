<?php
require __DIR__ . '/vendor/autoload.php';

define('APPLICATION_NAME', 'Google Calendar ADD unko');
define('CREDENTIALS_PATH', __DIR__ . '/add_unko.json');
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
$calendarId1 = 'XXXXXXXXXXXXXXXXXXXXXXXXX.calendar.google.com';
$optParams = array(
  'maxResults' => 50,
  'orderBy' => 'startTime',
  'timeMin' => date('c', strtotime('-2 week')),
  'singleEvents' => TRUE,
);

// 予定の取得
$results = $service->events->listEvents($calendarId1, $optParams);

if (count($results->getItems()) == 0) {
  print "No upcoming events found.\n";
} else {
    // 最新の一件のみ取り出す
    $last_time = $results->getItems();
    $last_result = end($last_time);
    if (empty($last_result->start->dateTime)) {
       $lastdate = $last_result->start->date;
    } else {
       $lastdate = $last_result->start->dateTime;
    }
    // 期間の計算
    $day = ceil( time() - strtotime((string) $lastdate)) / (60 * 60 * 24);
    $between = intval($day);
    $hour =  ceil( time() - strtotime((string) $lastdate)) / (60 * 60);
    $between_hr = intval($hour);
}

// 登録内容
$add_time = strtotime(getCurrentTime() . "+09:00");
$event = new Google_Service_Calendar_Event(array(
  'summary' => '💩('.$between.'日間)',
  'start' => array(
    'dateTime' =>  date('c', $add_time),
    'timeZone' => 'Asia/Tokyo',
  ),
  'end' => array(
    'dateTime' => date('c', $add_time),
    'timeZone' => 'Asia/Tokyo',
  ),
));

// カレンダー登録
// 3時間以内だと登録しない
if ($between_hr > 3) {
  // カレンダーへの登録
  $event = $service->events->insert($calendarId1, $event);
  echo "登録しました\n";
} else {
  echo "既に登録されています\n";
}

exit($between);

?>
