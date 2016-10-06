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

ini_set('display_errors', 'Off');
require_once 'google-api-php-client/src/Google/autoload.php';
require_once('vista/cargar_template_twig.php');
if($_SERVER['REQUEST_METHOD'] == "GET"){
	$id=$_GET["id"];
	$nombre=$_GET['nombre'];
	$msjError=$_GET["msjError"];
	load_template_twig("compartir_archivo.html", array('id' => $id, 'nombre' => $nombre, 'msjError' => $msjError));   
}elseif($_SERVER['REQUEST_METHOD'] == "POST"){
	session_start();
	if (isset($_SESSION['access_token'])){
		$id=$_POST["id"];
		$m=$_POST["mail"];		
		$mail= replace_mail($m);
		$nombre=$_POST["nombre"];
		$client = new Google_Client();
    	$client->setAccessToken($_SESSION['access_token']);
    	$drive_service = new Google_Service_Drive($client);
    
    	try{
			$drive_service->getClient()->setUseBatch(true);
		 	$batch = $drive_service->createBatch();

		  $userPermission = new Google_Service_Drive_Permission(array(
		    'type' => 'user',
		    'role' => 'writer',
		    'emailAddress' => $mail
		  ));

		 $request = $drive_service->permissions->create(
		    $id, $userPermission, array('fields' => 'id'));
		 $batch->add($request, 'user');
		 $results = $batch->execute();

		 foreach ($results as $result) {
    		if ($result instanceof Google_Service_Exception){
     			$msjError=$mail." no es un correo valido";
        	 	header("location:compartir_archivo.php?id=".$id."&nombre=".$nombre."&msjError=".urlencode($msjError));
    		} 
		}
		$msjExito="Fue enviada la invitacion para compartir el archivo con ".$mail;
		header("location:listar.php?msjExito=".urlencode($msjExito));
      }catch(Google_Service_Exception $e){ 
        	$msjError=$mail." no es un correo valido";
        	header("location:compartir_archivo.php?id=".$id."&nombre=".$nombre."&msjError=".urlencode($msjError));

           
        
      }
      
	}
}

?>