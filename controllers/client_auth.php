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
$isReg = false;
$authError = false;

if (isset($_SESSION["user_id"])) {
    header("Location: cart_page.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET["m"] == "reg") {
        $isReg = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["reg"])) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $address = $_POST["address"];
        $tel = $_POST["tel"];
        $sql = "INSERT INTO `clients`(`name`, `address`, `phoneNumber`, `email`) 
        VALUES ('$name','$address','$tel','$email')";
        if (!$db->query($sql)) {
            $authError = true;
            $isReg = true;
        } else {
            $_SESSION["user_id"] = $db->insert_id;
            header("Location: cart_page.php");
            exit;
        }
    } else if (isset($_POST["log"])) {
        $tel = $_POST["tel"];
        $sql = "SELECT `id` FROM `clients` WHERE `phoneNumber` = '$tel'";
        if ($result = $db->query($sql)) {
            if ($result->num_rows > 0) {
                $_SESSION["user_id"] = $result->fetch_assoc()["id"];
                $result->free();
                header("Location: cart_page.php");
                exit;
            } else {
                $authError = true;
            }
        }
    }
}

try {
    echo $view->render("client_auth.html.twig", array(
        "title" => "Авторизация",
        "isReg" => $isReg,
        "authError" => $authError,
    ));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}
