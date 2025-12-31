<?php

use LDAP\Result;

require_once '../repositories/UserRepository.php';
require_once '../Core/Auth.php';

class  UserService {

    private $userRepository;
    private $auth;

    public function __construct()
    {
        $this->userRepository=new UserRepository();
        $this->auth=new auth();
    }

    public function resgister($nom, $email, $password, $role = 'membre'){
        if($this->userRepository->emailExists($email)){
            throw new Exception("Email déjà utilisé");

        }
        $user = new user();
        $user -> setNom($nom)
              -> setEmail($email)
              ->setMotDePasse($password)
              ->setRole($role)
              ->setStatut('actif');
        $userId = $this->userRepository->ajout($user);
        return $this->auth->login($email, $password);
    }
    public function updateUserProfile($userId, $nom, $email){
        $user = $this->userRepository->findById($userId);
                if (!$user) {
            throw new Exception("Utilisateur non trouvé");
        }
                if ($user->getEmail() !== $email) {
            if ($this->userRepository->emailExists($email, $userId)) {
                throw new Exception("Email utilisé par un autre utilisateur");
            }
        }
        
        $user->setNom($nom)
             ->setEmail($email);
        
        return $this->userRepository->update($user);

    }
        public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new Exception("Utilisateur non trouvé");
        }
        
        if (!$user->verifyPassword($currentPassword)) {
            throw new Exception("Mot de passe actuel incorrect");
        }
        
        $user->setMotDePasse($newPassword);
        
        return $this->userRepository->update($user);
    }

        public function deactivateUser($userId) {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new Exception("Utilisateur non trouvé");
        }
        
        $user->setStatut('inactif');
        
        return $this->userRepository->update($user);
    }

        public function activateUser($userId) {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new Exception("Utilisateur non trouvé");
        }
        
        $user->setStatut('actif');
        
        return $this->userRepository->update($user);
    }

        public function getAllUsers() {
        return $this->userRepository->getAll();
    }
    
    public function getUserById($userId) {
        return $this->userRepository->findById($userId);
    }

    public function searchUsers($search){
        $users=$this->getAllUsers();
        $result = [];

        foreach($users as $user){
            if(stripos($user->getNom(),$search)!==false ||
            stripos($user->getEmail(),$search)!==false){
                $result=$user;
            }
        }
        return $result;
    }

    public function updateRole($newRole , $userId){
        $user = $this->userRepository->findById($userId);

        if(!$user){
            throw new Exception("Utilisateur non trouvé");
        }
        $role = ['admin', 'chef_projet', 'membre'];
        if(!is_array($newRole , $role)){
            throw new Exception("Rôle invalide");
        }
        $user->setRole($newRole);
        return $this->userRepository->update($user);
    }

        public function deleteUser($userId) {
        return $this->userRepository->delete($userId);
    }

}

?> 