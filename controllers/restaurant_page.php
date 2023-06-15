<?php
require_once "../db_connection.php";
require_once "../vendor/autoload.php";

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);

if(isset($_GET["id"])){
    $id = $_GET["id"];
    $sql = "SELECT * FROM `restaurants` WHERE `id` = $id";
    $result = $db->query($sql);
    if($result->num_rows > 0){
        $restaurant = $result->fetch_assoc();
        $sql = "SELECT * FROM `menu` WHERE `restaurantId` = $id";
        $result = $db->query($sql);
        if($result->num_rows > 0){
            $dishes = array();
            while($row = $result->fetch_assoc()){
                $dishes[] = $row;
            }
        }
    }
    else{
        $error = true;
    }
}
else{
    $error = true;
}

try {
    echo $view->render("restaurant-page.html.twig", array(
"title" => "Главная",
"items" => $recommended
    ));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}

?>