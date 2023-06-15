<?php

require_once "../db_connection.php";
require_once "../vendor/autoload.php";

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);

$sql = "SELECT * FROM restaurants";
$result = $db->query($sql);

if($result->num_rows > 0){
    $restaurants = array();
    while($row = $result->fetch_assoc()){
        $restaurants[] = $row;
    }
}
try{
    echo $view->render("restaurants-page.html.twig", array(
        "restaurants" => $restaurants
    ));
}
catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e){}


?>