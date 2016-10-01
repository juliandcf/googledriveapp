<?php
//ini_set('display_errors', 'On');
require_once 'google-api-php-client/autoload.php';

session_start();

if (isset($_SESSION['access_token'])){
  $client = new Google_Client();
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
  echo"error";
}