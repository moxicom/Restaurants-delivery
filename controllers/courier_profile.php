<?php
session_start();

require_once "../db_connection.php";
require_once "../vendor/autoload.php";

/**
 * @var $db
 */

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

session_start();

$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);

if (!isset($_SESSION["courier_id"])) {
    header("Location: courier_auth.php");
    exit();
}

$courier_id = $_SESSION["courier_id"];
$sql = "SELECT * FROM `couriers` WHERE id = '$courier_id'";
$courier = $db->query($sql)->fetch_assoc();
$isBusy = false;
$ordersArray = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["pick"])) {
        $order_id = $_POST["order_id"];
        $sql = "SELECT taken FROM `orders` WHERE order_id = $order_id";
        if ($result = $db->query($sql)) {
            if ($result->num_rows > 0) {
                if ($result->fetch_assoc()["taken"] == 0) {
                    $sql = "UPDATE `orders` SET taken = '1' WHERE order_id = '$order_id'";
                    $db->query($sql);
                    $sql = "UPDATE `couriers` SET order_id = '$order_id' WHERE id = '$courier_id'";
                    $db->query($sql);
                    $courier["order_id"] = $order_id;
                }
            }
        }
    }
}

if (!empty($courier["order_id"])) {
    $isBusy = true;
    $order_id = $courier["order_id"];
    $chosenOrder = array();
    $sql = "SELECT orders.dish_id, menu.dishName, orders.dish_amount 
            FROM `orders`
            JOIN `menu` ON menu.id = orders.dish_id
            WHERE orders.order_id = $order_id";
    if ($result = $db->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $chosenOrder["dishes"][] = array($row);
        }
    }
    $sql = "SELECT user_id FROM `orders` WHERE order_id = $order_id";
    $client_id = $db->query($sql)->fetch_assoc()["user_id"];
    $sql = "SELECT clients.name, clients.address, clients.phoneNumber
            FROM `clients` WHERE id = $client_id";
    $result = $db->query($sql)->fetch_assoc();
    $chosenOrder["address"] = $result["address"];
    $chosenOrder["tel"] = $result["phoneNumber"];
    $chosenOrder["name"] = $result["name"];

} else {
    $sql = "SELECT orders.order_id, orders.dish_id, menu.dishName, orders.dish_amount, clients.address 
    FROM `orders` 
    JOIN `menu` ON orders.dish_id = menu.id
    JOIN `clients` ON clients.id = orders.user_id
    WHERE orders.taken = 0";
    if ($result = $db->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $order_id = $row['order_id'];
            // print_r($row);
            // Проверяем, существует ли уже массив для данного заказа
            if (!isset($ordersArray[$order_id])) {
                $ordersArray[$order_id] = array();
            }

            // Добавляем информацию о блюде в массив заказа
            $orderItem = array(
//                'order_id' => $row['order_id'],
                'dish_id' => $row['dish_id'],
                'dish_name' => $row['dishName'],
                'dish_amount' => $row['dish_amount'],
                'address' => $row['address']
            );

            $ordersArray[$order_id][] = $orderItem;
        }
    }
}
//print_r($ordersArray);
try {
    echo $view->render("courier_profile.html.twig", array(
        "title" => "Авторизация",
        "isBusy" => $isBusy,
        "orders" => $ordersArray,
        "chosenOrder" => $chosenOrder,
    ));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}