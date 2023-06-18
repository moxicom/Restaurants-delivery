<?php
function getImageNameById(&$id, &$db){
    $nameSql = $db->query("SELECT * FROM images WHERE id = ".$id);
    if ($nameSql->num_rows > 0){
        $image_name = $nameSql->fetch_assoc()['image_name'];
    }else{
        $image_name = "no_image";
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
