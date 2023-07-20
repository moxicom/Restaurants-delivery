<?php

require_once "../db_connection.php";
require_once "functions.php";
require_once "../vendor/autoload.php";
/**
 * @include "db_connection.php"
 * @var $db
 */
use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);

// НУЖНО ДОБАВИТЬ ВЗЯТИЕ ВСЕХ РЕКОМЕНДУЕМЫХ ПРЕДМЕТОВ ИЗ БД:
// НАЗВАНИЕ -> ЦЕНА -> НАЗВАНИЕ КАРТИНКИ -> КАРТИНКА ИЗ ПАПКИ -> 
// ID ПРЕДМЕТА (для перехода на страницу товара) -> все добавить в массив

$sql = "SELECT COUNT(*) FROM `restaurants`";
$result = $db->query($sql);
$restAmount = $result->fetch_assoc()['COUNT(*)'];
$sql = "SELECT COUNT(*) FROM `menu`";
$result = $db->query($sql);
$dishesAmount = $result->fetch_assoc()['COUNT(*)'];
$sql = "SELECT AVG(cost) FROM `menu`";
$result = $db->query($sql);
$dish_avg_price = intval($result->fetch_assoc()['AVG(cost)']);

$sql = "SELECT * FROM `recommended`";
$result = $db->query($sql);
$recommended = array();

if($result->num_rows > 0){
    $recommended_id = array();
    while($row = $result->fetch_assoc()){
        $recommended_id[] = $row;
    }


    if(isset($recommended_id)) {
//        $recommended_id = array_reverse($recommended_id);
        for($i = 0; $i < count($recommended_id); $i++) {
            $sql = "SELECT * FROM `menu` WHERE `id` = ".$recommended_id[$i]['dish_id'];
            if($result = $db->query($sql)) {
                while($row = $result->fetch_assoc()) {
                    $recommended[] = $row;
                }    
            }
        }    
        for($i = 0; $i < count($recommended); $i++){
            $image_id = $recommended[$i]['image_id'];
            $recommended[$i]["image_name"] = getImageNameById($image_id, $db);
        }
    }
}

try {
    echo $view->render("index.html.twig", array(
"title" => "Главная",
"items" => $recommended,
"restaurantsAmount" =>$restAmount,
"dishesAmount" => $dishesAmount,
"avgPrice" => $dish_avg_price,
    ));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}

?>