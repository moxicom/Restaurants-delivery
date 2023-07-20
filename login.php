<?php
// password_hash($password, PASSWORD_BCRYPT)
session_start();

require_once "db_connection.php";

if (isset($_SESSION["logged"])) {
    header("location: /admin/admin_main.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
//    echo password_hash($password, PASSWORD_DEFAULT);
    if (empty($username)) {
        $error .= "<p class='error'>Please enter login.</p>";
    }
    if (empty($password)) {
        $error .=   "<p class='error'>Please enter your password.</p>";
    }
    if(empty($error)){
        $sql = "SELECT * FROM `admins` WHERE `username` = '$username'";
        $result = $db->query($sql);
        if($result->num_rows > 0){
            $row = mysqli_fetch_assoc($result);
            if(password_verify($password, $row['password'])){
                $_SESSION["logged"] =  "logged";
                $_SESSION["id"] = $row['id'];
                header("location: /admin/admin_main.php");
                exit;
            } else{
                $error .= "<p class='error'>The password is not valid.</p>";
            }
        }
        else{
            $error .= "<p class='error'>No User exist with that username.</p>";
        }
    }
    $db->close();
}   
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="./styles/favicon.ico">
</head>

<body>
        <div class="container" style="width: 500px; margin-top: 40px">
            <div class="row">
                <div class="col-md-12" style="justify-content: center;">
                    <h2 style="text-align: center">Login</h2>
                    <? echo $error ?>
                    <form action="" method="post">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required />
                        </div>    
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                        </div>
                    </form>

                </div>
            </div>
        </div>  
</body>

</html>