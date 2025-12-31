<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../entities/User.php';

class UserRepository{
    private $db;

    public function __construct()
    {
        $this->db=Database::connect();
    }

    public function ajout(user $user){
        $sql = "INSERT INTO users (nom, email, mot_de_passe, role, statut)
        VALUE (:nom, :email, :mot_de_passe, :role, :statut)";

        $ajout = $this->db->prepare($sql);
        $ajout->execute ([
            'nom' => $user -> getNom(),
            'email' => $user -> getEmail(),
            'mot_de_passe' => $user -> getMotDePasse(),
            'role' => $user -> getRole(),
            'statut' => $user -> getStatut()
        ]);
    }
    

    public function findById($id){
        $findById = $this->db->prepare("SELECT*FROM users WHERE id = :id");
        $findById->execute (['id'=>$id]);

        $data = $findById->fetch(PDO::FETCH_ASSOC);
        if($data){            
            $user = new user();
            $user->setId($data['id'])
                 ->setNom($data['nom'])
                 ->setEmail($data['email'])
                 ->setMotDePasse($data['mot_de_passe'])
                 ->setRole($data['role'])
                 ->setStatut($data['statut']);
            return $user;
        }
        return null;
    }

    public function findByEmail($email){
        $findByEmail = $this->db->prepare("SELECT*FROM users WHERE email = :email");
        $findByEmail->execute (['email'=>$email]);
        
        $data = $findByEmail->fetch(PDO::FETCH_ASSOC);
        if($data){
            $user=new user();
            $user->setId($data['id'])
                 ->setNom($data['nom'])
                 ->setEmail($data['email'])
                 ->setMotDePasse($data['mot_de_passe'])
                 ->setRole($data['role'])
                 ->setStatut($data['statut']);
            return $user;
        }
        return null;
    }

    public function getAll() {
        $sql = "SELECT * FROM users ORDER BY nom";
        $getAll = $this->db->query($sql);
        $users = [];
        
        while ($data = $getAll->fetch(PDO::FETCH_ASSOC)) {
            $user = new User();
            $user->setId($data['id'])
                 ->setNom($data['nom'])
                 ->setEmail($data['email'])
                 ->setMotDePasse($data['mot_de_passe'])
                 ->setRole($data['role'])
                 ->setStatut($data['statut']);
            $users[] = $user;
        }
        
        return $users;
    }

        public function update($user) {
        $sql = "UPDATE users 
                SET nom = :nom, 
                    email = :email, 
                    mot_de_passe = :mot_de_passe, 
                    role = :role, 
                    statut = :statut 
                WHERE id = :id";
        
        $update = $this->db->prepare($sql);
        return $update->execute([
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
            'mot_de_passe' => $user->getMotDePasse(),
            'role' => $user->getRole(),
            'statut' => $user->getStatut()
        ]);
    }

        public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $delete = $this->db->prepare($sql);
        return $delete->execute([':id' => $id]);
    }
        public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $params = [':email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }

}


?>