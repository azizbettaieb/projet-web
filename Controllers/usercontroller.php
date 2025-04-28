<?php
include 'C:\xampp\htdocs\web\db.php';
include 'C:\xampp\htdocs\web\Models\user.php';

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
    
    public function login($email, $password) {
        // Préparation de la requête pour récupérer l'utilisateur
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Vérification si l'utilisateur existe et si le mot de passe est correct
        if ($user && password_verify($password, $user['password'])) {
            // Création de la session pour l'utilisateur connecté
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_lastname'] = $user['lastName']; 
            $_SESSION['photo'] = $user['photo'];
            $_SESSION['statuscompte'] = $user['statuscompte'];


    
            // Redirection en fonction du rôle de l'utilisateur
            if ($_SESSION['user_role'] == 1) {
                header("Location: ../../Views/backOffice/app-profile.php");  // Redirection vers le back-office
            } else {
                header("Location: ../frontOffice/profile.php");  // Redirection vers le front-office
            }
            exit();  // Assurez-vous que le script s'arrête après la redirection
        } else {
            // Redirection en cas d'erreur d'authentification avec un message d'erreur
            header("Location: ../FrontOffice/login.php");
            exit();
        }
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
}

?>
