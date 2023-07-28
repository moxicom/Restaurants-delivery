<?php
require_once("../controllers/functions.php");

function isImageValid(&$image): bool
{
	$mime = mime_content_type($image["tmp_name"]);
	if (!(strpos($mime, "image") === 0)) {
		return false;
	}
	$fileSize = $image["size"];
	if ($fileSize > 2 * 1024 * 1024) {
		return false;
	}
	return true;
}
function addImage(&$image, &$db, &$image_error, &$insertedImageId){
	// Проверяем тип файла

	if (isImageValid($image)) {

		// Создаем уникальное имя файла
		$filename = uniqid() . '_' . $image['name'];

		// Сохраняем загруженный файл на удаленный сервер
		$remoteDir = '../uploaded_images/'; // Замените на нужный путь на удаленном сервере
		$remotePath = $remoteDir . $filename;

		if (move_uploaded_file($image['tmp_name'], $remotePath)) {
			// inserting to table images
			$sql = "INSERT INTO `images`(`image_name`) VALUES ('$filename')";
			$result = $db->query($sql);
			if($result === false){
				$image_error = true;
			}
			else{
				$insertedImageId = $db->insert_id;
				// echo 'File uploaded successfully.';
			}
		} else {
			// echo 'Error uploading file.';
			$image_error = true;
		}
	} else {
		// echo 'Ошибка в типе файла. Или размер файла больше 2 Мб';
		$image_error = true;
	}
}
function deleteImageById(&$id, &$db) : bool {
	$sql = "SELECT * FROM `images` WHERE `id` = $id";
	$result = $db->query($sql);
	if($result->num_rows > 0){
		$image_name = $result->fetch_assoc()["image_name"];
		$filePath = "../uploaded_images/".$image_name;
		if ($image_name != "no_image.jpg" && file_exists($filePath)) {
			// echo "previous file is exsist in folder:".$filePath."<br>";
			chmod($filePath, 0644);
			if(!unlink($filePath)){
				return false;
			}
		}
//		else{
//			$id = 0;
//		}
	}
//	else{
//		$id = 0;
//	}
	return true;
}
function updateImage(&$id, &$db, &$image, &$errorText) : bool { // returns true if success
	// echo "id before alchemy = $id"."<br>";
	if(isImageValid($image)){
		// echo "new image is valid"."<br>";
		if(deleteImageById($id, $db)){
			// echo "old image deleted from server"."<br>";
			$filename = uniqid() . '_' . $image['name'];
			$remoteDir = '../uploaded_images/';
			$remotePath = $remoteDir.$filename;
			if (move_uploaded_file($image['tmp_name'], $remotePath)) {
				$sql = "SELECT * FROM `images` WHERE `id`=$id";
				$result =  $db->query($sql);
				if($result->num_rows <= 0){
					$id = 0;
				}
				// echo "id after alchemy = $id"."<br>";
				// echo "filename = $filename"."<br>";
				if($id == 0){
					// echo "deleted old image from db"."<br>";
					$sql = "INSERT INTO `images`(`image_name`) VALUES ('$filename')";
					if($db->query($sql)){
						$id = $db->insert_id;
						// echo "Inserted new image to db with id $id"."<br>";
						return true;
					}
					else{
						$errorText = "Ошибка добавления изображения в базу данных, но на сервер оно было загружено";
						return false;
					}
				}
				else { // images has been found
					$sql = "UPDATE `images` SET `image_name`='$filename' WHERE `id` = $id";
					if($db->query($sql)){
						// echo "updated image"."<br>";
						return true;
					}
					else{
						$errorText = "Не удалось обновить изображение";
						return false;
					}
				}
			}
			else{
				$errorText = "Ошибка загрузки изображения на сервер";
				return false;
			}
		}
		else{
			$errorText = "Ошибка удаления старого изображения";
			return false;
		}
	}
	else{
		$errorText = "Новое изображение не прошло валидацию. Размер файла должен быть не больше 2 Мб";
		return false;
	}
}
function deleteDishById(&$id, &$db, &$errorText = null) : bool {
	// УДАЛИТЬ ИЗОБРАЖЕНИЕ -> УДАЛИТЬ ЕГО ИЗ БД -> УДАЛИТЬ СТРОКУ О БЛЮДЕ В БД.
	// ФУНКЦИИ, КОТОРЫЕ УЖЕ ЕСТЬ:
	// deleteImageById. Удаляет из сервера
	$dish = null;
	if(getDishById($dish, $id, $db)){
		$image_id = $dish["image_id"];
		if(deleteImageById($image_id, $db)){
			if($db->query("DELETE FROM `images` WHERE `id`=$image_id")){
				if($db->query("DELETE FROM `recommended` WHERE `dish_id`=$id")){
					if($db->query("DELETE FROM `menu` WHERE `id`=$id")){
						return true;
					}
					$errorText = "Изображение удалено. Не удалось удалить блюдо из базы данных. Id = $id";
					return false;
				}
				$errorText = "Изображение удалено. Не удалось удалить блюдо из рекомендованного. Попробуйте сделать это вручную";
				return false;
			}
			$errorText = "Изображение удалено с сервера, но не из базы данных. Не удалось удалить блюдо из базы данных";
			return false;
		}
		$errorText = "Не удалось удалить изображение из сервера";
		return false;
	}
	$errorText = "Такого блюда не существует";
	return false;
}
function deleteRestaurantById(&$id, &$db, &$errorText = null) : bool{
	//СНАЧАЛА УДАЛИТЬ ИЗОБРАЖЕНИЕ РЕСТОРАНА. ПОТОМ ПРОЙТИСЬ ПО ВСЕМ БЛЮДАМ. УДАЛИТЬ СТРОЧКУ РЕСТОРАНА
	if(!restaurantExist($id, $db)){
		$errorText = "Данного ресторана не существует";
		return false;
	}
	$restaurant = getRestaurantById($id, $db);
	$image_id = $restaurant["image_id"];
	if(!deleteImageById($image_id, $db)){
		$errorText = "Не удалось удалить изображение из сервера";
		return false;
	}
	if(!($db->query("DELETE FROM `images` WHERE `id`=$image_id"))){
		$errorText = "Изображение удалено с сервера, но не из базы данных. Не удалось удалить блюдо из базы данных";
		return false;
	}
	$dishes = array();
	$result = $db->query("SELECT * FROM `menu` WHERE `restaurantId` = $id");
	while ($dish = $result->fetch_assoc()) {
		if(!deleteDishById($dish["id"], $db, $errorText)){
			return false;
		}
	}
	if(!($db->query("DELETE FROM `restaurants` WHERE `id`=$id"))){
		$errorText .= "Все составляющие ресторана удалены. Но возникла ошибка при удалении подробностей ресторана";
		return false;
	}
	return true;
}

function getAllOrders(&$db) {
    $ordersArray = array();
    $sql = "SELECT orders.order_id, orders.dish_id, menu.dishName, orders.dish_amount, clients.address, orders.taken
    FROM `orders` 
    JOIN `menu` ON orders.dish_id = menu.id
    JOIN `clients` ON clients.id = orders.user_id";
    if ($result = $db->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $order_id = $row['order_id'];
            // print_r($row);
            // Проверяем, существует ли уже массив для данного заказа
            if (!isset($ordersArray[$order_id])) {
                $ordersArray[$order_id] = array();
            }

            // Добавляем информацию о блюде в массив заказа
            $orderItem = array(
//                'order_id' => $row['order_id'],
                'dish_id' => $row['dish_id'],
                'dish_name' => $row['dishName'],
                'dish_amount' => $row['dish_amount'],
                'address' => $row['address'],
                'taken' => $row['taken']
            );
            $ordersArray[$order_id][] = $orderItem;
        }
    }
//    print_r($ordersArray);
    return $ordersArray;
}