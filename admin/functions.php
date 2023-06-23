<?php

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
				echo 'File uploaded successfully.';
			}
		} else {
			echo 'Error uploading file.';
			$image_error = true;
		}
	} else {
		echo 'Ошибка в типе файла. Или размер файла больше 2 Мб';
		$image_error = true;
	}
}
function deleteImageById(&$id, &$db){
	$sql = "SELECT * FROM `images` WHERE `id` = $id";
	$result = $db->query($sql);
	if($result->num_rows > 0){
		$image_name = $result->fetch_assoc()["image_name"];
		$filePath = "../uploaded_images/".$image_name;
		if ($image_name != "no_image.jpg" && file_exists($filePath)) {
			echo "previous file is exsist in folder:".$filePath."<br>";
			chmod($filePath, 0644);
			if(!unlink($filePath)){
				return false;
			}
		}
		else{
			$id = 0;
		}
	}
	else{
		$id = 0;
	}
	return true;
}
