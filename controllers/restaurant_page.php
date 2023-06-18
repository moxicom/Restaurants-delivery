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
            for($i = 0; $i < count($dishes); $i++){
                $image_id = $dishes[$i]['image_id'];
                $dishes[$i]["image_name"] = getImageNameById($image_id, $db);
                // $nameSql = $db->query("SELECT * FROM images WHERE id = ".$id);
                // if ($nameSql->num_rows > 0){
                //     $dishes[$i]['image_name'] = $nameSql->fetch_assoc()['image_name'];
                // }else{
                //     $dishes[$i]['image_name'] = "no_image";
                // }
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
// $restaurant["restaurantName"]
try {
    echo $view->render("restaurant-page.html.twig", array(
"title" => $restaurant["restaurantName"],
"header" => $restaurant["restaurantName"],
"dishes" => $dishes
    ));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}

?>