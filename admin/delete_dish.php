<?php
session_start();

require_once("../db_connection.php");
require_once("../vendor/autoload.php");
require_once ("functions.php");

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/**
 * @include "../db_connection.php"
 * @var $db
 */

if (!isset($_SESSION["logged"])) {
	// Обработка ошибки авторизации
	header("location: ../login.php");
	exit;
}


$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);
$errorText = null;

if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_POST["dishId"])){
		if(deleteDishById($_POST["dishId"], $db, $errorText)){
			$restaurantId = $_POST["restaurantId"];
			$db->close();
			header("location: restaurant_page.php?id=$restaurantId");
			exit;
		}
	}
}
else{
	$errorText = "Отсутствует запрос";
}


try {
	echo $view->render("error-page.html.twig", array(
		"errorText" => $errorText,
	));
} catch (Twig\Error\LoaderError|Twig\Error\RuntimeError|Twig\Error\SyntaxError $e) {
}
