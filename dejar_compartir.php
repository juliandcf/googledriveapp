<?php
ini_set('display_errors', 'On');
require_once 'google-api-php-client/src/Google/autoload.php';
require_once('vista/cargar_template_twig.php');

session_start();
if (isset($_SESSION['access_token'])){
    $id=$_GET["id"];
    $client = new Google_Client();
    $client->setAccessToken($_SESSION['access_token']);
    $drive_service = new Google_Service_Drive($client);
    
    /*Los distintos tipos de mime https://developers.google.com/drive/v3/web/mime-types */
    //print_r($consulta);
     //

    $optParams = array(  
     'fields' => 'owners(permissionId), permissions(id)'
      );
    $archivo = $drive_service->files->get($id, $optParams);
    $permiso_propietario=($archivo->owners)[0]['permissionId'];
    
    $permisos_a_borrar=[];
    $permisos_archivo=$archivo->getPermissions();
    $drive_service->getClient()->setUseBatch(true);
    $batch = $drive_service->createBatch();
    foreach ($permisos_archivo as $perm) {
      if($perm['id'] != $permiso_propietario){
        array_push($permisos_a_borrar, $perm['id']);
        $batch->add($drive_service->permissions->delete($id, $perm['id']));
      } 
    }
    $batch->execute();
    // Falta verificar error
  $msjExito="El archivo ya no está compartido";
  header("location:listar.php?msjExito=".urlencode($msjExito));

} else {
            $msjError=$mail."No se pudo dejar de compartir el archivo";
            header("location:listar.php?&msjError=".urlencode($msjError));

}

?>