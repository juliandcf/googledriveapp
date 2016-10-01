<?php

require_once 'google-api-php-client/autoload.php';

session_start();
if (isset($_SESSION['access_token'])){

  $nombre=$_POST["nombre"];
  $compartir=$_POST["compartir"];

  $client = new Google_Client();
  $client->setAccessToken($_SESSION['access_token']);
  $drive_service = new Google_Service_Drive($client);

  /*Los distintos tipos de mime https://developers.google.com/drive/v3/web/mime-types */
  $fileMetadata = new Google_Service_Drive_DriveFile(array(
  'name' => $nombre,
  'mimeType' => 'application/vnd.google-apps.document'));
  $file = $drive_service->files->create($fileMetadata, array(
  'fields' => 'id, name')); 

  printf("File ID creado: %s\n", $file->id);

  $drive_service->getClient()->setUseBatch(true);
  $batch = $drive_service->createBatch();

  $userPermission = new Google_Service_Drive_Permission(array(
    'type' => 'user',
    'role' => 'writer',
    'emailAddress' => $compartir
  ));

 $request = $drive_service->permissions->create(
    $file->id, $userPermission, array('fields' => 'id'));
 $batch->add($request, 'user');
 $results = $batch->execute();
  foreach ($results as $result) {
    if ($result instanceof Google_Service_Exception) {
      // Handle error
      printf($result);
    } else {
      printf("Permission ID: %s\n", $result->id);
    }
  }

} else {
  echo"error";

}

?>