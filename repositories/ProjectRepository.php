<?php
require_once __DIR__ . '/../entities/Projet.php';

class ProjetRepository {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    public function create($projet) {  
        $sql = "INSERT INTO projets (titre, description, chef_id, statut) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $projet->getTitre(),
            $projet->getDescription(),
            $projet->getChefId(),
            $projet->getStatut()
        ]);

        $projet->setId($this->db->lastInsertId());
        return $projet;
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM projets WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        
        return $data ? projet::fromArray($data) : null; 
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM projets ORDER BY date_creation DESC");
        $projets = [];
        
        while ($data = $stmt->fetch()) {
            $projets[] = projet::fromArray($data); 
        }
        
        return $projets;
    }

    public function update($projet) {  
        $sql = "UPDATE projets SET 
                titre = ?, description = ?, chef_id = ?, statut = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $projet->getTitre(),
            $projet->getDescription(),
            $projet->getChefId(),
            $projet->getStatut(),
            $projet->getId()
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM projets WHERE id = ?");
        return $stmt->execute([$id]);
    }
}