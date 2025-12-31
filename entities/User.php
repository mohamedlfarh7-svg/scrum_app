<?php

class User {
    private $id;
    private $nom;
    private $email;
    private $mot_de_passe;
    private $role;
    private $statut;
    
    public function getId() {
        return $this->id;
    }
    
    public function getNom() {
        return $this->nom;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getMotDePasse() {
        return $this->mot_de_passe;
    }
    
    public function getRole() {
        return $this->role;
    }
    
    public function getStatut() {
        return $this->statut;
    }

    public function setId($id){
        $this -> id= $id;
        return $this;
    }
    
    public function setNom($nom){
        $this->nom=$nom;
        return $this;
    }
    public function setEmail($email){
        $this->email=$email;
        return $this;
    }
        public function setMotDePasse($mot_de_passe) {
        $this->mot_de_passe = $mot_de_passe;
        return $this;
    }
    
    public function setRole($role) {
        $this->role = $role;
        return $this;
    }
    
    public function setStatut($statut) {
        $this->statut = $statut;
        return $this;
    }

        public function verifyPassword($password) {
        return password_verify($password, $this->mot_de_passe);
    }
    
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

}
?>

