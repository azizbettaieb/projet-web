<?php

include_once  '../../config.php';
include_once  '../../Controller/usercontroller.php';
$pdo = config::getConnexion();

$controller = new UserController($pdo);

$controller->logout();
?>