<?php

session_start();

require_once("../db_connection.php");
require_once("../controllers/functions.php");
require_once("../vendor/autoload.php");
require_once("functions.php");

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);

/**
 * @include "../db_connection.php"
 * @var $db
 */

if (!isset($_SESSION["logged"]))
{
	// Обработка ошибки авторизации
	header("location: ../login.php");
	exit;
} else
{
	$errorGet = false;
	$dish = array();
	$errorText = null;
	$twigPath = "admin-edit-dish.html.twig";
	if (isset($_GET["id"]))
	{
		$id = $_GET["id"];
		if(!getDishById($dish,$id, $db)){
			$errorText = "Данное блюдо не найдено";
			$errorGet = true;
		}
	} else
	{
		$errorGet = true;
	}

	if (isset($_POST["saveDishId"]))
	{
		$id = $_POST["saveDishId"];
		$name = $_POST["name"];
		$description = $_POST["description"];
		$price = $_POST["price"];
		$sql = "SELECT * FROM `menu` WHERE `id`='$id'";
		$result = $db->query($sql);
		if($result){
			$row = $result->fetch_assoc();
			$restaurantId = $row["restaurantId"];
			$sql = "UPDATE `menu` SET `dishName`='$name',`description`='$description',`cost`='$price' WHERE `id`='$id'";
			if($db->query($sql)){
				header("location: restaurant_page.php?id=$restaurantId");
				exit;
			}
			else {
				$errorText = "Ошибка внесения изменений в базу данных";
			}
		}
		else{
			$errorText = "Не удалось получить данные о блюде";
		}
	}
	elseif (isset($_POST["updateImageDishId"])){
		$id = $_POST["updateImageDishId"];
		$image = $_FILES["image"];
		$sql = "SELECT * FROM `menu` WHERE `id` = $id";
		$result = $db->query($sql);
		if($result){
			$dish = $result->fetch_assoc();
			$image_id = $dish["image_id"];
			if(updateImage($image_id, $db,$image , $errorText)){
				$sql = "UPDATE `menu` SET `image_id`=$image_id WHERE id=$id";
				if($db->query($sql)){
					$restaurantId = $dish["restaurantId"];
					header("location: restaurant_page.php?id=$restaurantId");
					exit;
				}
				else{
					$errorText = "Не удалось обновить изображение";
				}
			}
			else{
				$errorText = "Не удалось обновить изображение";
			}
		}
		else{
			$errorText = "Не удалось получить данные о блюде";
		}
	}
	$db->close();
	try {
		echo $view->render($twigPath, array(
			"errorGet" => $errorGet,
			"errorText" => $errorText,
			"dish" => $dish,
		));
	} catch (Twig\Error\LoaderError|Twig\Error\RuntimeError|Twig\Error\SyntaxError $e) {
	}
}