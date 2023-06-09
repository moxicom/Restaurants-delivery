<?php
session_start();

require_once("../db_connection.php");

if (!isset($_SESSION["logged"])) {
    // Обработка ошибки авторизации
    header("location: ../login.php");
    exit;
}

if (isset($_POST["dishId"])) {
    $dishId = $_POST["dishId"];
    
    // Удаление записи из таблицы `recommended`
    $db->query("DELETE FROM recommended WHERE dish_id = $dishId");
    
    // Перенаправление пользователя после удаления записи
    header("location: admin_main.php");
    exit;
} else {
    // Обработка ошибки
    header("HTTP/1.1 400 Bad Request");
    exit;
}
?>