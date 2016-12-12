<?php
require __DIR__ . '/vendor/autoload.php';

define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('CREDENTIALS_PATH', '~/.credentials/calendar-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client.json');
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR_READONLY)
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


function printResult($results,$start_time,$end_time) {
	if (count($results->getItems()) == 0) {
	  print "No upcoming events found.\n";
	} else {
	  foreach ($results->getItems() as $event) {
	    $start = $event->start->dateTime;
	    if (empty($start)) {
	      $start = $event->start->date;
	    }

	    if (strtotime($start) >= $start_time && strtotime($start) <= $end_time) {
              if (date("G", strtotime($start))==0) {
	        echo $event->getSummary() . "。\n";
	      } else {
	        echo date("G時", strtotime($start)) . "、" . $event->getSummary() . "。\n";
              }
	    }
	    
	  }
	}
}


// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);

//今日の日付の範囲
$start_time = strtotime(getCurrentDate() . "0:00:00");
$end_time = strtotime(getCurrentDate() . "23:59:59");

// ここにカレンダーIDを入れる
$calendarId1 = 'primary';
$calendarId2 = 'xxxxxxxxxxxxxxxxxxx';

$optParams = array(
  'maxResults' => 10,
  'orderBy' => 'startTime',
  'singleEvents' => TRUE,
  'timeMin' => date('c', $start_time),
  'timeMax' => date('c', $end_time),
);

$results = $service->events->listEvents($calendarId1, $optParams);
if (count($results->getItems()) != 0) {
  echo "XXXXXさんの予定。。\n";
  printResult($results,$start_time,$end_time);
}

$results = $service->events->listEvents($calendarId2, $optParams);
if (count($results->getItems()) != 0) {
  echo "XXXXさんの予定。。\n";
  printResult($results,$start_time,$end_time);
}
