<?php
ini_set('display_errors', 'On');
require_once 'google-api-php-client/src/Google/autoload.php';

require_once('vista/cargar_template_twig.php');

session_start();

if (isset($_SESSION['access_token'])){
  load_template_twig("agregar_archivo.html", array());   
} else {
  echo"error";
}