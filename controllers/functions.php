<?php
function getImageNameById(&$id, &$db){
    $nameSql = $db->query("SELECT * FROM images WHERE id = ".$id);
    if ($nameSql->num_rows > 0){
        $image_name = $nameSql->fetch_assoc()['image_name'];
	    $filePath = "../uploaded_images/".$image_name;
//	    .".jpg";
		if(!file_exists($filePath)){
			$image_name = "no_image.jpg";
		}
    }else{
        $image_name = "no_image.jpg";
    }
    return $image_name; 
}

function getAllRestaurants(&$db): array
{
    $result = $db->query("SELECT * FROM `restaurants`");
    $restaurants = array();
    while($row = $result->fetch_assoc()){
        $restaurants[] = $row;
    }
    return $restaurants;
}

function getRestaurantById($id, &$db){
	$sql = "SELECT * FROM `restaurants` WHERE id = ".$id;
	$result = $db->query($sql);
	if($result->num_rows > 0){
		$restaurant = $result->fetch_assoc();
		$image_id = $restaurant["image_id"];
		$restaurant["image_name"] = getImageNameById($image_id, $db);
		return $restaurant;
	}
	else{
		return null;
	}
}

function restaurantExist(&$id, &$db){
	$sql = "SELECT * FROM `restaurants` WHERE `id` = $id";
	$result = $db->query($sql);
	if($result->num_rows > 0){
		return true;
	}
	return false;
}
