<?php

require_once "../../db_connection.php";

/**
 * @var $db
 */

session_start();

if (!isset($_SESSION["courier_id"])) {
    header("Location: ../courier_auth.php");
    exit();
}

$courier_id = $_SESSION["courier_id"];
$sql = "SELECT * FROM `couriers` WHERE id = '$courier_id'";
$courier = $db->query($sql)->fetch_assoc();

if (!empty($courier["order_id"])) {
    $order_id = $courier["order_id"];
//    $sql = "UPDATE `orders` SET `taken` = 2 WHERE order_id = $order_id";
    $sql = "DELETE FROM `orders` WHERE order_id = $order_id";
    $db->query($sql);
    $sql = "UPDATE `couriers` SET `order_id` = NULL WHERE id = $courier_id";
    $db->query($sql);
}
header("Location: ../courier_profile.php");
exit();