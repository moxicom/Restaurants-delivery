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
$ordersArray = array();
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
    $user_id = $_SESSION["user_id"];
    $sql = "SELECT orders.order_id, menu.dishName, orders.dish_amount, orders.taken 
    FROM `orders` JOIN `menu` ON orders.dish_id = menu.id WHERE orders.user_id = $user_id";
    if($result = $db->query($sql)){
        while ($row = $result->fetch_assoc()) {
            $order_id = $row['order_id'];

            // Проверяем, существует ли уже массив для данного заказа
            if (!isset($ordersArray[$order_id])) {
                $ordersArray[$order_id] = array();
            }

            // Добавляем информацию о блюде в массив заказа
            $orderItem = array(
                'dish_amount' => $row['dish_amount'],
                'taken' => $row['taken'],
                'dishName' => $row['dishName']
            );

            $ordersArray[$order_id][] = $orderItem;
        }
        print_r($ordersArray);
    }
}

try {
	echo $view->render("cart.html.twig", array(
		"title" => "Корзина",
		"header" => "Корзина",
		"cart" => $dishes,
		"amount" => $cart,
        "userLogged" => $user_logged,
        "orders" => $ordersArray,
	));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}