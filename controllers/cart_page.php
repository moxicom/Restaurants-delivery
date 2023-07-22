<?php
require_once "../db_connection.php";
require_once "functions.php";
require_once "../vendor/autoload.php";

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

session_start();

$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);

/**
 * @var $db
 */
$dishes = array();
$cart = array();
$user_logged = false;


if(isset($_SESSION["cart"])){
	$cart = $_SESSION["cart"];
	foreach ($cart as $dish_id => $quantity){
		$dish = array();
		if(getDishById($dish, $dish_id, $db)){
			$dish["image_name"] = getImageNameById($dish["image_id"], $db);
			$dishes[] = $dish;
		}
	}
}
if (isset($_SESSION["user_id"])) {
    # make request to get all orders
    $user_logged = true;
}

try {
	echo $view->render("cart.html.twig", array(
		"title" => "Корзина",
		"header" => "Корзина",
		"cart" => $dishes,
		"amount" => $cart,
        "userLogged" => $user_logged,
	));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}