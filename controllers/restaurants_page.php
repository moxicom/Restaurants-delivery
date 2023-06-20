<?php

require_once "../db_connection.php";
require_once "./functions.php";
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
        $image_id = $row["image_id"];
        $row["image_name"] = getImageNameById($image_id, $db);
        $restaurants[] = $row;
    }
}
try{
    echo $view->render("restaurants-page.html.twig", array(
        "restaurants" => $restaurants,
	    "header" => "Рестораны",
	    "title" => "Рестораны"
    ));
}
catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e){}


?>