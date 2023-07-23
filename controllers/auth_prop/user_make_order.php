<?php

session_start();

require_once "../../db_connection.php";
/**
 * @var $db
 */

function generateUniqueOrderId() {
    // Получаем текущее время в микросекундах
    $microtime = microtime();
    // Удаляем точку и преобразуем в строку
    $timestamp = str_replace('.', '', $microtime);
    // Получаем случайное число от 100 до 999
    $randomNumber = mt_rand(100, 999);
    // Комбинируем время и случайное число, чтобы получить уникальный ID заказа
    $orderId = $timestamp . $randomNumber;
    return (int) $orderId; // Преобразуем в целое число и возвращаем
}

if (isset($_SESSION["cart"])) {
    $cartItems = array();
    foreach ($_SESSION["cart"] as $product_id => $quantity) {
        if ($quantity > 0) {
            $cartItems[] = array("product_id" => $product_id, "quantity" => $quantity);
        }
    }
    if (!empty($cartItems)) {
        # добавить в бд.
        $user_id = $_SESSION["user_id"];
        $order_id = generateUniqueOrderId();
        echo $order_id;
        foreach ($cartItems as $item) {
            $product_id = $item["product_id"];
            $quantity = $item["quantity"];
            $sql = "INSERT INTO `orders`(`order_id`, `user_id`, `dish_id`, `dish_amount`) VALUES ('$order_id', '$user_id', '$product_id', '$quantity')";
            if($db->query($sql)) {
                unset($_SESSION["cart"]);
            }
        }
    }
}
header("Location: ../cart_page.php");
exit();