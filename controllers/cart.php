<?php
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
	if($_POST["delete"]){
		$product_id = $_POST["product_id"];
	    if(isset($_SESSION["cart"][$product_id])){
	        unset($_SESSION["cart"][$product_id]);
	    }
	}
	elseif($_POST["check"]){
		$product_id = $_POST["product_id"];
		if(isset($_SESSION["cart"][$product_id])){
			$response["status"] = "success";
		}
		else{
			$response["status"] = "empty";
		}
		header("Content-Type: application/json");
		print_r(json_encode($response));
	}
	else {
		$product_id = $_POST["product_id"];
		function addToCart($product_id) {
			if (!isset($_SESSION["cart"])) {
				$_SESSION["cart"] = array();
			}

			if (isset($_SESSION["cart"][$product_id])) {
				$_SESSION["cart"][$product_id] += 1;
			} else {
				$_SESSION["cart"][$product_id] = 1;
			}
		}
		addToCart($product_id);
	}
}
elseif($_SERVER["REQUEST_METHOD"] == "GET"){
	function getCartItems() {
		$cartItems = array();

		if (isset($_SESSION["cart"])) {
			foreach ($_SESSION["cart"] as $product_id => $quantity) {
				// Здесь вы можете выполнить запрос к базе данных для получения информации о товаре по его ID
				// В данном примере мы просто добавляем ID товара и количество в массив
				$cartItems[] = array("product_id" => $product_id, "quantity" => $quantity);
			}
		}

		return $cartItems;
	}
	$cartItems = getCartItems();
	header("Content-Type: application/json");
	print_r(json_encode($cartItems));
}
