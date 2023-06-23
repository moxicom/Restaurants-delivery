<?php
session_start();

require_once("../db_connection.php");
require_once("../controllers/functions.php");
require_once ("../vendor/autoload.php");
require_once ("functions.php");


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
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		echo "post"."<br>";
		$image_error = false;
		$insertedImageId = 0;
		if ($_FILES["image"]["error"] === UPLOAD_ERR_OK) {
			$image = $_FILES["image"];
			addImage($image, $db, $image_error, $insertedImageId);
		}
		else{
			echo "image error"."<br>";
			$image_error = true;
		}
		$name = $_POST["name"];
		$address = $_POST["address"];
		$cuisine = $_POST["cuisine"];
		$number = $_POST["number"];
		$email = strtolower($_POST["email"]);
//		echo $name."<br>".$address."<br>".$cuisine."<br>".$number."<br>".$email."<br>".$insertedImageId."<br>";
		if($image_error){
			$insertedImageId = 0;
		}
		$sql = "INSERT INTO `restaurants`(`restaurantName`, `address`, `cuisine`, `phoneNumber`,`email`, `image_id`) VALUES ('$name','$address','$cuisine','$number','$email','$insertedImageId')";
		$result = $db->query($sql);
		$db->close();
	}
}

try {
	echo $view->render("admin-create-restaurant.html.twig", array(
		'main_header' => 'Создание ресторана',
	));
} catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e) {
}