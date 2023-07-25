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

$authError = false;

if (isset($_SESSION["courier_id"])) {
    header("Location: courier_profile.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tel = $_POST["tel"];
    $sql = "SELECT id FROM `couriers` WHERE phoneNumber = '$tel'";
    $result = $db->query($sql);
    if($result->num_rows > 0) {
        $id = $result->fetch_assoc()["id"];
        $_SESSION["courier_id"] = $id;
        header("Location: courier_profile.php");
        exit();
    } else {
        $authError = true;
    }
}


try {
    echo $view->render("courier_auth.html.twig", array(
        "title" => "Авторизация",
        "authError" => $authError,
    ));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}
