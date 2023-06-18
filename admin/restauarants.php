<?php 
session_start();

require_once("../db_connection.php");
require_once ("../controllers/functions.php");
require_once "../vendor/autoload.php";

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);

/**
 * @include "../db_connection.php"
 * @var $db
 */

if (!isset($_SESSION["logged"])) {
    // Обработка ошибки авторизации
    header("location: ../login.php");
    exit;
}
else{
    $restaurants = getAllRestaurants($db);
    try {
        echo $view->render("admin-restaurants.html.twig", array(
            'main_header' => 'Рестораны',
            'restaurants' => $restaurants
        ));
    } catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
    }
}
?>
