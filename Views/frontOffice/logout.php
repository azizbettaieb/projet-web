<?php
include 'C:\xampp\htdocs\web\db.php';
include 'C:\xampp\htdocs\web\Controllers\usercontroller.php';

$controller = new UserController($pdo);

$controller->logout();
?>