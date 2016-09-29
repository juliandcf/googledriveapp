<?php
require_once 'google-api-php-client/autoload.php';

session_start();
$client = new Google_Client();
$client->setAuthConfigFile('client_secrets.json');
$client->addScope("https://www.googleapis.com/auth/drive");
$nombre=$_POST["nombre"];
$compartir=$_POST["compartir"];
echo($_POST["nombre"]);
$client->setAccessToken($_SESSION['access_token']);
 $drive_service = new Google_Service_Drive($client);


/*Creo el archivo*/

/*Los distintos tipos de mime https://developers.google.com/drive/v3/web/mime-types */
$fileMetadata = new Google_Service_Drive_DriveFile(array(
  'name' => $nombre,
  'mimeType' => 'application/vnd.google-apps.document'));

$file = $drive_service->files->create($fileMetadata, array(
  'fields' => 'id', 'name'));


printf("File ID creado: %s\n", $file->id);

/*Le doy permisos al archivo*/
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






?>