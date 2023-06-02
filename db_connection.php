<?php



$db_hostname = 'localhost';
$db_database = 'rest_delivery';
$db_username = 'root';
$db_password = '';

$db = new mysqli($db_hostname, $db_username, $db_password, $db_database);

if(!$db){
    die("Error connection to server". mysqli_connect_error());
}
?>