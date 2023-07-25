<?php

session_start();
if (isset($_SESSION["courier_id"])) {
    unset($_SESSION["courier_id"]);
}
header("Location: ../courier_auth.php");
exit();