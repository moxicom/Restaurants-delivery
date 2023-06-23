<?php
session_start();

require_once("../db_connection.php");
require_once ("../controllers/functions.php");
require_once ("../vendor/autoload.php");
require_once ("functions.php");

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

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
	$errorGet = false;
	$restaurant = array();
	$errorText = null;
	$twigPath = "admin-edit-restaurant.html.twig";
	if(isset($_GET["id"])){
		$id = $_GET["id"];
		$restaurant = getRestaurantById($id, $db);
		if($restaurant == null){
			$error = true;
		}
	}
	else{
		$errorGet = true;
	}

	if(isset($_POST["saveRestId"])){
		$id = $_POST["saveRestId"];
		echo "save rest";
		$name = $_POST["name"];
		$address = $_POST["address"];
		$cuisine = $_POST["cuisine"];
		$number = $_POST["number"];
		$email = strtolower($_POST["email"]);
		$sql = "";
//		$result = $db->query($sql);
//		if($result === false){
//			$errorText = "Ошибка внесения изменений";
//		}
//		$db->close();
	}
	elseif (isset($_POST["updateImageRestId"])){
		$id = $_POST["updateImageRestId"];
		// echo "update image"."<br>";
		$image = $_FILES["image"];
		$sql = "SELECT * FROM `restaurants` WHERE `id` = $id";
		$result = $db->query($sql);
		if($result->num_rows > 0){
			// echo "id found"."<br>";
			$image_id = $result->fetch_assoc()["image_id"];
			if(updateImage($image_id, $db, $image, $errorText)){
				$sql = "UPDATE `restaurants` SET `image_id`=$image_id WHERE id=$id";
				echo "image updated"."<br>";
				if($db->query($sql)){
					echo "success"."<br>";
					header("location: restaurant_page.php?id=$id");
					exit;
				}
				else{
					$errorText .= " Ошибка внесения изменений";
				}
			}
//			$new_image_id = $old_image_id;
//			if(isImageValid($image)){
//				echo "name is valid"."<br>";
//				if(deleteImageById($new_image_id, $db)){
//					echo "deleted"."<br>";
//					$filename = uniqid() . '_' . $image['name'];
//					$remoteDir = '../uploaded_images/';
//					$remotePath = $remoteDir.$filename;
//					if (move_uploaded_file($image['tmp_name'], $remotePath)) {
//						if($new_image_id != 0){
//							echo "updated image"."<br>";
//							$sql = "UPDATE `images` SET `image_name`='$filename' WHERE `id` = $new_image_id";
//							$result = $db->query($sql);
//						}
//						else{
//							$sql = "DELETE FROM `images` WHERE id=$old_image_id";
//							$result = $db->query($sql);
//							echo "deleted old image from db"."<br>";
//							$sql = "INSERT INTO `images`(`image_name`) VALUES('$filename')";
//							$result = $db->query($sql);
//							$image_id = $db->insert_id;
//							echo "inserted new image"."<br>";
//							$sql = "UPDATE `restaurants` SET `image_id`=$image_id WHERE id=$id";
//							$result = $db->query($sql);
//							echo "updated id of image"."<br>";
//						}
//
//
//					}
//					else{
//						$errorText = "Старый файл был удален, но новый не удалось загрузить";
//					}
//				}
//				else{
//					$errorText = "Ошибка обновления изображения";
//				}
//			}
//			else{
//				$errorText = "Ошибка загрузки файла. Он должен иметь размер не больше 2 Мб";
//			}
		}
	}
	try {
		echo $view->render($twigPath, array(
			"errorGet" => $errorGet,
			"errorText" => $errorText,
			"restaurant" => $restaurant,
		));
	} catch (Twig\Error\LoaderError|Twig\Error\RuntimeError|Twig\Error\SyntaxError $e) {
	}
}
