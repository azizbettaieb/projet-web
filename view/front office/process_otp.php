<?php
session_start();
require_once '../../GoogleAuthenticator.php';
require_once '../../config.php'; // adjust path as needed
$pdo = config::getConnexion();

if (!isset($_SESSION['pending_user']) || !isset($_POST['otp'])) {
    header("Location: login.php");
    exit();
}

$otp = $_POST['otp'];
$user = $_SESSION['pending_user'];

$gAuth = new PHPGangsta_GoogleAuthenticator();
$check = $gAuth->verifyCode($user['twofa_secret'], $otp, 2); // 2 = 2*30s window

if ($check) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_lastname'] = $user['lastName'];
    $_SESSION['photo'] = $user['photo'];
    $_SESSION['statuscompte'] = $user['statuscompte'];
    
    unset($_SESSION['pending_user']);

    if ($user['role'] == 1) {
        header("Location: ../../View/back/app-profile.php");
    } else {
        header("Location: ../front office/profile.php");
    }
    exit();
} else {
    header("Location: verify2fa.php?error=invalid_code");
    exit();
}
