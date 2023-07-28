<?php
session_start();
require_once("../db_connection.php");

/**
 * @var $db
 */

if (!isset($_SESSION["logged"])) {
    header("location: ../login.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["order_id"])) {
        $order_id = $_POST["order_id"];
        echo $order_id;
        $sql = "SELECT taken FROM `orders` WHERE order_id = $order_id";
        $result = $db->query($sql);
        if ($result->num_rows > 0) {
            if ($result->fetch_assoc()["taken"] == 0) {
                $sql = "DELETE FROM `orders` WHERE order_id = $order_id";
                if ($db->query($sql)) {
                    echo "deleted";
                }
            }
        }
    }
}
header("Location: admin_main.php");