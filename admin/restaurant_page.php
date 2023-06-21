<?php
session_start();

require_once("../db_connection.php");
require_once ("../controllers/functions.php");
require_once "../vendor/autoload.php";

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
else{
	$error = false;
//	$restaurantIsEmpty = false;
	$restaurant = array();
	$dishes = array();
	if(isset($_GET["id"])){
		$id = $_GET["id"]; // id ресторана
		$restaurant = getRestaurantById($id, $db);
		if($restaurant == null){
			$error = true;
		}
		$restaurant["image_name"] = getImageNameById($restaurant["image_id"], $db);

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
			}
		}
	}
	try {
		echo $view->render("admin-restaurant.html.twig", array(
		"restaurant" => $restaurant,
		"dishes" => $dishes,
		"error" => $error,
		));
	} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
	}
}