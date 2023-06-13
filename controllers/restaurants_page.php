<?php

require_once "../db_connection.php";
require_once "../vendor/autoload.php";

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);

echo $view->render("restaurants-page.html.twig");

?>