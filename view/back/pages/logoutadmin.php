<?php
include_once "../../../config.php";
include '../../../Controller/usercontroller.php';
$pdo = config::getConnexion();

$controller = new UserController($pdo);

$controller->logoutadmin();
?>