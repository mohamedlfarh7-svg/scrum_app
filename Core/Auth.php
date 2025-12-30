<?php 
require_once __DIR__ ."Database.php";

class auth {
    private $db ;
     
    public function __construct()
    {
        $this->db=Database::connect();

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

    }




    public function register($email,$password,$nom){

        $checkEmail = $this->db->prepare("SELECT id FROM users WHERE email=?");
        $checkEmail->execute([$email]);
        if($checkEmail->fetch()){
            throw new Exception("Email already exists");
        }
        $hash = password_hash($password,PASSWORD_DEFAULT);

        $insert = $this->db->prepare(
            "INSERT INTO users (nom,email,hash) VALUES (?,?,?)"
        );
        $insert->execute([$nom],[$email],[$hash]);
        
    } 


    public function login($email,$password){

        $checkEmail2 = $this->db->prepare("SELECT*FROM users WHERE email=?");
        $checkEmail2->execute([$email]);

        $user = $checkEmail2->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password , $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            return true;
        }return false;

        
    }

    public function isLogged(){
        return isset($_SESSION['user_id']);
    }

    public function logout(){
        session_destroy();
    }

}
?>