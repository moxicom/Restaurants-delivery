<?
session_start();

require_once("../db_connection.php");
require_once ("functions.php");

/**
 * @include "../db_connection.php"
 * @var $db
 */

if (!isset($_SESSION["logged"])) {
    header("location: ../login.php");
    exit;
} else {

    // showing recommended list
    $result = $db->query("SELECT * FROM recommended");
    $dishes_id = $result->fetch_all(MYSQLI_ASSOC);
    $dishes = array();
    foreach ($dishes_id as $dish_id) {
        $id = $dish_id['dish_id'];
        $result = $db->query("SELECT * FROM `menu` WHERE id = $id");
        $dishes[] = $result->fetch_assoc();
    }
    $recommended = '';
    foreach ($dishes as $dish) {
        $recommended .= '<tr>';
        $recommended .= '<th scope="row">' . $dish['id'] . '</th>';
        $recommended .= '<td>' . $dish['dishName'] . '</td>';
        $recommended .= '<td>' . $dish['description'] . '</td>';
        $recommended .= '<td>₽' . $dish['cost'] . '</td>';
        $recommended .= '<td>';
        $recommended .= '<form method="POST" action="delete_recommended.php">';
        $recommended .= '<input type="hidden" name="dishId" value="' . $dish['id'] . '">';
        $recommended .= '<button type="submit" class="btn btn-primary btn-sm btn-block" role="button" aria-pressed="true">Удалить</button>';
        $recommended .= '</form>';
        $recommended .= '</td>';
        $recommended .= '</tr>';
    }

    // Получить все заказы
    $ordersArray = getAllOrders($db);
    $orders = '';
    foreach ($ordersArray as $order_id => $order) {
        $orders .= '<tr>';
        $orders .= '<th scope="row">' . $order_id . '</th>';
        $orders .= '<td>';
        foreach ($order as $item) {
          $orders .= $item['dish_id'].'-'.$item['dish_name'].'-'.$item['dish_amount'].' шт.'.'<br>';
            print_r($item);
        }
        if ($order[0]["taken"] != 0) {
            $disabled = 'disabled="disabled"';
        }
        $orders .= '</td>';
        $orders .= '<td>' . $order[0]['address'] . '</td>';
        $orders .= '<td>';
        $orders .= '<form method="POST" action="delete_order.php">';
        $orders .= '<input type="hidden" name="order_id" value="' . $order_id . '">';
        $orders .= '<input type="submit" class="btn btn-primary btn-sm btn-block" role="button"'.$disabled.' value="delete" >';
        $orders .= '</form>';
        $orders .= '</td>';
        $orders .= '</tr>';
    }

// Проверка нажатия кнопки и отображение формы
    $rest_form = "";
    if(isset($_POST["start_chosing"])) {
        $result = $db->query("SELECT * FROM restaurants");
        $restaurants = $result->fetch_all(MYSQLI_ASSOC);
        $rest_form.='<form method="post">';
        $rest_form.='<select class="form-select" name="restaurant" aria-label="Default select example">';
        foreach($restaurants as $restaurant){
            $rest_form.='<option value="'.$restaurant['id'].'" name = "restaurant_id">'.$restaurant['restaurantName'].'</option>';
        }
        $rest_form.='</select>';
        $rest_form.='<br>';
        $rest_form.='<input type="submit" name="rest_chosed" value="Выбрать">';
        $rest_form.='<input type="submit" name="cancel" value="Отмена">';
        $rest_form.='</form>';
    
    }
    elseif($_POST["cancel"]){
        try{
            $_SESSION["restaurant_id"] = $rest_id;
            $_SESSION["restaurant_name"] = $rest_name;
        }catch(Exception $ex){}
    }
    elseif(isset($_POST["rest_chosed"])){
        try{
            $_SESSION["restaurant_id"] = $rest_id;
            $_SESSION["restaurant_name"] = $rest_name;
        }catch(Exception $ex){}
        // echo $_POST["restaurant"].'<br>';
        $rest_id = $_POST["restaurant"];
        $result = $db->query("SELECT * FROM `restaurants` WHERE `id` = $rest_id");
        $rest_name = $result->fetch_assoc()['restaurantName'];
        $_SESSION["restaurant_id"] = $rest_id;
        $_SESSION["restaurant_name"] = $rest_name;
        // print_r($rest_name);
        $rest_form.='<form method="post">';
        $rest_form.='<p>'.$rest_name.' (при изменении нажать на "Обновить блюда")'.'</p>';
        $rest_form.='<input type="submit" name="start_chosing" value="Выбрать другой ресторан">';

        $rest_form.='</form>';

        $rest_form.='<br>';

        $rest_form.='<form method="post" style="mergin: 0 auto">';
        $rest_form.='<p>Выберите блюдо</p>';
        $rest_form.='<select class="form-select" name="dishes" aria-label="Default select example">';
        $result = $db->query("SELECT * FROM menu WHERE `restaurantId` = $rest_id");
        $dishes_to_add = $result->fetch_all(MYSQLI_ASSOC);
        foreach($dishes_to_add as $_dish){
            $rest_form.='<option value="'.$_dish["id"].'" name = "dish_id">'.$_dish['dishName'].'</option>';
        }
        $rest_form.='</select>';
        $rest_form.='<br>';
        $rest_form.='<input type="submit" name="add_chosen" value="Добавить в рекомендуемое">';
        $rest_form.='<br>';
        $rest_form.='<input type="submit" name="cancel" value="Отмена">';
        $rest_form.='</form>';
    }
    elseif(isset($_POST["add_chosen"])){
        try{
            $id = $_POST["dishes"];
            if($result = $db->query("SELECT * FROM `recommended` WHERE `dish_id` = $id")){
                if($result->num_rows > 0){
                    $error = "<p>Данное блюдо уже рекомендовано</p>";
                } else{
                    $db->query("INSERT INTO `recommended`(`dish_id`) VALUES($id)");
                }
            }
            
        }catch(Exception $ex){}
        header("Refresh:0");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin panel</title>
<!--    <meta http-equiv="X-UA-Compatible" content="IE=edge">-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="icon" type="image/x-icon" href="../styles/favicon.ico">
    <link rel="stylesheet" href="../styles/admin-styles.css">
</head>
<style>
    body{
        margin: 0;
        padding: 0;
        background: linear-gradient(to bottom right, #E3F0FF, #FAFCFF);
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
</style>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p style="margin-top:40px"><strong> Панель администратора
                    </strong> <a href="logout.php" style="float: right;" class="btn btn-secondary btn-sm active" role="button" aria-pressed="true">Log Out</a></p>
            </div>
        </div>
        <div class="white-main-container" style="padding: 20px 40px; margin: 10px 0">
            <div class="row">
                <h2 style="margin-bottom: 10px">Рекомендуемые блюда</h2>
                <table class="table table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Название</th>
                            <th scope="col">Описание</th>
                            <th scope="col">Цена</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $recommended ?>
                    </tbody>
                </table>
            </div>
            <?php
            if(isset($rest_form) && $rest_form != ""){
                echo $rest_form;
            }
            else{
                echo $error;
                echo '<form method="post">';
                echo '<input type="submit" name="start_chosing" value="Добавить">';
                echo '</form>';
            }
            ?>
            <a href="restaurants.php" class="btn btn-primary btn-lg btn-block" role="button"
               style="margin-top: 50px">Перейти к настройкам ресторанов</a>
        </div>

        <div class="white-main-container" style="padding: 20px 40px; margin: 20px 0">
            <div class="row">
                <h2 style="margin-bottom: 10px">Заказы</h2>
                <table class="table table-hover">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Блюда</th>
                        <th scope="col">Адрес</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php echo $orders ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>