<?php
require_once 'google-api-php-client/autoload.php';

session_start();
$client = new Google_Client();
$client->setAuthConfigFile('client_secrets.json');
$client->addScope("https://www.googleapis.com/auth/drive");
$nombre=$_POST["nombre"];
echo($_POST["nombre"]);
$client->setAccessToken($_SESSION['access_token']);
 $drive_service = new Google_Service_Drive($client);

$fileMetadata = new Google_Service_Drive_DriveFile(array(
  'name' => $nombre,
  'mimeType' => 'application/vnd.google-apps.document'));
$file = $drive_service->files->create($fileMetadata, array(
  'fields' => 'id', 'name'));
printf("File ID: %s\n", $file->id);


?>