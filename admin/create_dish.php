<?php
session_start();

require_once("../db_connection.php");
require_once("../controllers/functions.php");
require_once ("../vendor/autoload.php");
require_once ("functions.php");


use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);

/**
 * @include "../db_connection.php"
 * @var $db
 */
if (!isset($_SESSION["logged"])) {
	// Обработка ошибки авторизации
	header("location: ../login.php");
	exit;
}

$errorText = null;
$getError = false;
$id = null;
if($_SERVER["REQUEST_METHOD"] == "GET"){
	if(!isset($_GET["id"])){
		$getError = true;
		$errorText = "Ресторан не выбран";
	}
	elseif (isset($_GET["id"])){
		$id = $_GET["id"];
		if(!restaurantExist($id, $db)){
			$getError = true;
			$errorText = "Такой ресторан не существует";
		}
	}
}
elseif($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_POST["restId"])){
		$restaurantId = $_POST["restId"];
		echo $restaurantId."<br>";
		$name = $_POST["name"];
		$description = $_POST["description"];
		$price = $_POST["cost"];
		$image = $_FILES["image"];
		$sql = "INSERT INTO `menu`(`dishName`, `description`, `cost`, `restaurantId`, `image_id`) VALUES ('$name',
          	'$description', '$price', '$restaurantId', '0')";
		if($db->query($sql)){ // if dish is inserted
			echo "Добавлено в бд";
			$image_id = 0;
			$dish_id = $db->insert_id;
			if(updateImage($image_id, $db,$image , $errorText)){
				$sql = "UPDATE `menu` SET `image_id`=$image_id WHERE id=$dish_id";
				if($db->query($sql)){
					header("location: restaurant_page.php?id=$restaurantId");
					exit;
				}
				else{
					$errorText .= "Блюдо и изображение загружено на сервер. Ошибка добавления изображения в базу данных";
				}
			}
		}
		else{
			$errorText .= "Не удалось добавить блюдо";
		}
	}
}

try {
	echo $view->render("admin-create-dish.html.twig", array(
		'main_header' => 'Создание блюда',
		'errorText' => $errorText,
		'id' => $id,
		'getError' => $getError,
	));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}
