<?php
require_once "db_connection.php";
require_once "vendor/autoload.php";

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

$loader = new FilesystemLoader("templates");
$view = new Environment($loader);

// НУЖНО ДОБАВИТЬ ВЗЯТИЕ ВСЕХ РЕКОМЕНДУЕМЫХ ПРЕДМЕТОВ ИЗ БД:
// НАЗВАНИЕ -> ЦЕНА -> НАЗВАНИЕ КАРТИНКИ -> КАРТИНКА ИЗ ПАПКИ -> 
// ID ПРЕДМЕТА (для перехода на страницу товара) -> все добавить в массив

$sql = "SELECT * FROM recommended";
$result = $db->query($sql);

if($result->num_rows > 0){
    $recommended_id = array();
    while($row = $result->fetch_assoc()){
        $recommended_id[] = $row;
    }


    if(isset($recommended_id)) {
        $recommended_id = array_reverse($recommended_id);
        for($i = 0; $i < count($recommended_id); $i++) {
            $sql = "SELECT * FROM `menu` WHERE `id` = ".$recommended_id[$i]['dish_id'];
            if($result = $db->query($sql)) {
                while($row = $result->fetch_assoc()) {
                    $recommended[] = $row;
                }    
            }
        }    
        for($i = 0; $i < count($recommended); $i++){
            $id = $recommended[$i]['image_id'];
            $nameSql = $db->query("SELECT * FROM images WHERE id = ".$id);
            if ($nameSql->num_rows > 0){
                $recommended[$i]['image_name'] = $nameSql->fetch_assoc()['image_name'];
            }else{
                $recommended[$i]['image_name'] = "no_image";
            }
        }
    }
}
else{
    echo "0 ЗАПИСЕЙ";
}

try {
    echo $view->render("index.html.twig", array(
"title" => "Главная",
"items" => $recommended
    ));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}

?>