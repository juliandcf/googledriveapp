<?php
require_once 'google-api-php-client/autoload.php';
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

session_start();

$client = new Google_Client();
$client->setAuthConfigFile('client_secrets.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/grupo10/index.php');
$client->addScope("https://www.googleapis.com/auth/drive.file");

if (!isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  print_r($_SESSION);
  header("Location: http://localhost/grupo10/menu.html");
}

?>