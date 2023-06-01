<?php
use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

require_once 'vendor/autoload.php';

$loader = new FilesystemLoader('templates');
$view = new Environment($loader);
echo $view->render('index.twig');