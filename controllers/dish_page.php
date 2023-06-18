<?php 

require_once "../db_connection.php";
require_once "./functions.php";
require_once "../vendor/autoload.php";

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);

if(isset($_GET["id"])){
    $id = $_GET["id"];
    $sql = "SELECT * FROM `menu` WHERE `id` = $id";
    $result = $db->query($sql);
    if($result->num_rows > 0){
        $dish = $result->fetch_assoc();
        $image_id = $dish["image_id"];
        $dish["image_name"] = getImageNameById($image_id, $db);
    }
    else{
        $error = true;
    }   
}

try {
    echo $view->render("item-page.html.twig", array(
"title" => $dish["dishName"],
"header" => $dish["dishName"],
"dish" => $dish
    ));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}

?>