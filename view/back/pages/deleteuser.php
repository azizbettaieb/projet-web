<?php
include_once "../../../config.php";
include '../../../Controller/usercontroller.php';
$pdo = config::getConnexion();

$controller = new UserController($pdo);
$id = $_GET['id'] ?? null;

if ($id && $controller->deleteUser($id)) {
    header("Location: data-user.php");
    exit;
} else {
    echo "Failed to delete user.";
}
?>
