<?php 
require_once __DIR__ . '/Database.php';

class auth {
    private $db;
     
    public function __construct() {
        $this->db = Database::connect();

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register($nom, $email, $password, $role = 'membre') {
        $roles_autorises = ['admin', 'chef_projet', 'membre'];
        if (!in_array($role, $roles_autorises)) {
            $role = 'membre';
        }

        $checkEmail = $this->db->prepare(
            "SELECT id FROM users WHERE email = ?"
        );
        $checkEmail->execute([$email]);

        if ($checkEmail->fetch()) {
            throw new Exception("Cet email est déjà utilisé");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
    
        $insert = $this->db->prepare(
            "INSERT INTO users (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)"
        );

        return $insert->execute([$nom, $email, $hash, $role]);
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = ? AND statut = 'actif'"
        );
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
        
        return false;
    }

    public function isLogged() {
        return isset($_SESSION['user_id']);
    }

    public function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }

    public function isAdmin() {
        return $this->getUserRole() === 'admin';
    }

    public function isChefProjet() {
        return $this->getUserRole() === 'chef_projet';
    }

    public function isMembre() {
        return $this->getUserRole() === 'membre';
    }

    public function logout() {
        session_destroy();
    }
}
?>