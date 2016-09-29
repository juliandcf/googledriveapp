<?php
require_once 'google-api-php-client/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfigFile('client_secrets.json');
$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->addScope("https://www.googleapis.com/auth/drive");

if (isset($_SESSION['access_token']) && $_SESSION['access_token']){
  $client->setAccessToken($_SESSION['access_token']);
  $drive_service = new Google_Service_Drive($client);
	
/*Se puede pasar parametros al listFiles como el $optParams para filtrar: quedaria algo asi
  
  $optParams = array(
      'pageSize' => 10,
     'fields' => 'nextPageToken, files(id, name)'
  );
  $results = $drive_service->files->listFiles($optParams);
*/
  $results = $drive_service->files->listFiles();

	if (count($results->getFiles()) == 0) {
	  print "No files found.\n";
	} else {
	  print "Files:\n<br>";
	  foreach ($results->getFiles() as $file) {
	    printf("%s (%s)\n", $file->getName(), $file->getId()); echo"<br>";
	  }
	}
} else {
  //$authUrl = $client->createAuthUrl();
  //print "<a class='login' href='$authUrl'>Conectar</a>";	
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/grupo10/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}