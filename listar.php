<?php
ini_set('display_errors', 'Off');
require_once 'google-api-php-client/src/Google/autoload.php';

require_once('vista/cargar_template_twig.php');

session_start();

if (isset($_SESSION['access_token'])){
  $client = new Google_Client();
  $client->setAccessToken($_SESSION['access_token']);
  $drive_service = new Google_Service_Drive($client);
  /*
  Para hacer consultas!!
  https://developers.google.com/drive/v2/web/search-parameters
'q' => "name contains 'Prueba'", 
  */
  $optParams = array(
      'q' => "",
     'corpus' => 'domain',
     'fields' => 'files(id,name,capabilities(canShare),shared, parents)'
  );
  $results = $drive_service->files->listFiles($optParams);
  
  $archivos=[];
   foreach ($results->getFiles() as $file) {
      $arch_temp=[
      "nombre" => $file->getName(),
      "id" => $file->getId(),
      "compartido" => $file->shared,
      ];
      array_push($archivos, $arch_temp);
    }
    
  $msjExito=$_GET["msjExito"];
  $msjError=$_GET["msjError"];
 load_template_twig("listar_archivos.html", array('files' => $archivos, 'msjExito' => $msjExito, 'msjError' => $msjError));   

} else {
  echo"error";
}