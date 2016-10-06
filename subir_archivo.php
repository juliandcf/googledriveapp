<?php
function replace_mail ($mail)
    {
    if(preg_match('/@/',$mail)){
      $m=substr($mail, 0, strpos($mail,"@"));
    }else{
      $m=$mail;
    }
    return $m."@gmail.com";
    };
ini_set('display_errors', 'On');
require_once 'google-api-php-client/src/Google/autoload.php';
require_once('vista/cargar_template_twig.php');


if($_SERVER['REQUEST_METHOD'] == "GET"){
  $msjError=$_GET["msjError"];
  load_template_twig("agregar_archivo.html", array('msjError' => $msjError));   
}elseif($_SERVER['REQUEST_METHOD'] == "POST"){
    session_start();
    if (isset($_SESSION['access_token'])){

        $nombre=$_POST["nombre"];
        $client = new Google_Client();
        $client->setAccessToken($_SESSION['access_token']);
        $drive_service = new Google_Service_Drive($client);

        /*Los distintos tipos de mime https://developers.google.com/drive/v3/web/mime-types */
        $fileMetadata = new Google_Service_Drive_DriveFile(array(
        'name' => $nombre,
        'mimeType' => 'application/vnd.google-apps.document'));
        $file = $drive_service->files->create($fileMetadata, array(
        'fields' => 'id, name')); 

        $msjExito="El archivo fue subido a Google Drive correctamente";
      if(isset($_POST["comparte"])){
          $m=$_POST["mail"];
          $mail=replace_mail($m);
          $comparte=$_POST["comparte"];
          $drive_service->getClient()->setUseBatch(true);
          $batch = $drive_service->createBatch();

          $userPermission = new Google_Service_Drive_Permission(array(
            'type' => 'user',
            'role' => 'writer',
            'emailAddress' => $mail
          ));

         $request = $drive_service->permissions->create(
            $file->id, $userPermission, array('fields' => 'id'));
         $batch->add($request, 'user');
         $results = $batch->execute();
          $msjExito="El archivo fue subido a Google Drive correctamente y compartido con ".$mail;

          foreach ($results as $result) {
            if ($result instanceof Google_Service_Exception) {
               $msjError=$mail."No se pudo subir el archivo";
               header("location:subir_archivo.php?&msjError=".urlencode($msjError));
            } 
          }
      }

      header("location:listar.php?msjExito=".urlencode($msjExito));

    } else {
      $msjError=$mail."No se pudo subir el archivo";
               header("location:subir_archivo.php?&msjError=".urlencode($msjError));

    }
}

?>