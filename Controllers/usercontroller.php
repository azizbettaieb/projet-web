<?php
include 'C:\xampp\htdocs\web\db.php';
include 'C:\xampp\htdocs\web\Models\user.php';
require_once 'C:\xampp\htdocs\web\GoogleAuthenticator.php';
class UserController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

  

    public function createUser($name, $lastName, $email, $password, $role , $statusCompte, $photoName) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO utilisateur (name, lastName, email, password, role, statuscompte, photo) VALUES (?, ?, ?, ?, ?,?, ?)");
        return $stmt->execute([$name, $lastName, $email, $hashedPassword, $role, $statusCompte, $photoName]);
    }
    public function createclient($name, $lastName, $email, $password , $photoName) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO utilisateur (name, lastName, email, password, role , statuscompte, photo) VALUES (?, ?, ?, ?, 0,0, ?)");
        return $stmt->execute([$name, $lastName, $email, $hashedPassword ,$photoName]);
    }

    // Read all users
    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT * FROM utilisateur");
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row['id'], $row['name'], $row['lastName'], $row['email'],  null, $row['role'],  $row['statusCompte'], $row['photo'] );
        }
        return $users;
    }

    // Read a single user by ID
    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User($row['id'], $row['name'], $row['lastName'], $row['email'], null, $row['role']);
        }
        return null;
    }

    // Update an existing user
    public function updateUser($id, $name, $lastName, $email, $role) {
        $stmt = $this->pdo->prepare("UPDATE utilisateur SET name = ?, lastName = ?, email = ?, role = ? WHERE id = ?");
        return $stmt->execute([$name, $lastName, $email, $role, $id]);
    }

    // Delete a user
    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateur WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function login($email, $password, $otp = null) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($password, $user['password'])) {
            // If 2FA is enabled
            if (!empty($user['twofa_secret'])) {
                if (!$otp) {
                    session_start();
                    $_SESSION['pending_user'] = $user;
                    header("Location: ../FrontOffice/verify2fa.php");
                    exit();
                }
    
                $gAuth = new PHPGangsta_GoogleAuthenticator();
                $checkResult = $gAuth->verifyCode($user['twofa_secret'], $otp, 2);
                if (!$checkResult) {
                    header("Location: ../FrontOffice/login.php?error=otp_failed");
                    exit();
                }
            }
    
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_lastname'] = $user['lastName'];
            $_SESSION['photo'] = $user['photo'];
            $_SESSION['statuscompte'] = $user['statuscompte'];
    
            if ($_SESSION['user_role'] == 1) {
                header("Location: ../../Views/backOffice/app-profile.php");
            } else {
                header("Location: ../FrontOffice/profile.php");
            }
            exit();
        }
    
        header("Location: ../FrontOffice/login.php?error=login_failed");
    }
    
    public function disable2FA($userId) {
    // Remove the 2FA secret from the database
    $stmt = $this->pdo->prepare("UPDATE utilisateur SET twofa_secret = NULL WHERE id = ?");
    $stmt->execute([$userId]);

    // You can also handle any session variables or redirection after disabling 2FA
    return true;
}

    
public function logout() {
    session_start();

    // Clear all session variables
    session_unset();
    
    // Destroy the session
    session_destroy();
    
    // Clear the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    header("Location: ../FrontOffice/login.php");
}


public function enable2FA($userId) {
    $gAuth = new PHPGangsta_GoogleAuthenticator();
    $secret = $gAuth->createSecret();

    // Save to DB
    $stmt = $this->pdo->prepare("UPDATE utilisateur SET twofa_secret = ? WHERE id = ?");
    $stmt->execute([$secret, $userId]);

    // Generate QR Code URL
    $qrCodeUrl = $gAuth->getQRCodeGoogleUrl('MyAppName', $secret);

    return $qrCodeUrl;
}

}

?>
