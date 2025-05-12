<?php
session_start();
include_once '../../config.php';
require_once  '../../Controller/usercontroller.php';
require_once '../../GoogleAuthenticator.php';
$pdo = config::getConnexion();

$userController = new UserController($pdo); // Initialize the controller

// Check if the user is logged in and has the correct permissions
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the form has been submitted
if (isset($_POST['disable_2fa'])) {
    $userId = $_SESSION['user_id'];

    // Disable 2FA for the user
    if ($userController->disable2FA($userId)) {
        // Redirect the user back to the profile page with a success message
        header("Location: profile.php?success=2fa_disabled");
    } else {
        // Handle any error that occurs
        header("Location: profile.php?error=failed_to_disable_2fa");
    }
}
?>
