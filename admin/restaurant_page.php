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
	if(isset($_GET["id"]) && !empty($_GET["id"])){
		$id = $_GET["id"];
		$restaurant = getRestaurantById($id, $db);
		if($restaurant == null){
			$error = true;
		}
		$restaurant["image_name"] = getImageNameById($restaurant["image_id"], $db);
	}
	try {
		echo $view->render("admin-restaurant.html.twig", array(
		"restaurant" => $restaurant,
		"error" => $error
		));
	} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
	}
}