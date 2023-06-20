<?php
require_once "../db_connection.php";
require_once "./functions.php";
require_once "../vendor/autoload.php";

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

$loader = new FilesystemLoader("../templates");
$view = new Environment($loader);
$search_text = "пустой поиск";
$error = "";
/**
 * @var $db
 */
$restaurants = array();
if(isset($_GET["search"]) && !empty($_GET["search"])){
	$search_text = trim($_GET["search"]);
	$sql = "SELECT * FROM `restaurants` WHERE UPPER(restaurantName) LIKE UPPER('%$search_text%')";
	$result = $db->query($sql);
	if($result->num_rows > 0){
		$restaurants = array();
		while($row = $result->fetch_assoc()){
			$image_id = $row["image_id"];
			$row["image_name"] = getImageNameById($image_id, $db);
			$restaurants[] = $row;
		}
	}
	else{
		$error = " - ничего не найдено";
	}
}
else{
	$error = " - ничего не найдено";
}

try{
	echo $view->render("search.html.twig", array(
		"title" => $search_text,
//		mb_convert_case(mb_strtolower($search_text, 'UTF-8'), MB_CASE_TITLE, "UTF-8") - первая заглавная буква
		"header" => "Результат поиска: ".mb_strtolower($search_text, 'UTF-8'),
		"restaurants" => $restaurants,
		"error" => $error
	));
}
catch (\Twig\Error\LoaderError|\Twig\Error\RuntimeError|\Twig\Error\SyntaxError $e){}