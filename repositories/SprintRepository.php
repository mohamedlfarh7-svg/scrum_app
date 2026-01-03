<?php
require_once __DIR__ . '/../entities/Sprint.php';
class SprintRepository{
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function create($sprint){
        $sql = "INSERT INTO sprints (titre, projet_id, date_debut, date_fin, statut) 
        VALUES (?, ?, ?, ?, ?)";
        $create=$this->db->prepare($sql);
        $create->execute([
            $sprint->getTitre(),
            $sprint->getProjetId(),
            $sprint->getDateDebut(),
            $sprint->getDateFin(),
            $sprint->getStatut()
        ]);
        $sprint->setId($this->db->lastInsertId());
        return $sprint;
    }
    public function findById($id){
        $findId=$this->db->prepare("SELECT * FROM sprints WHERE id = ?");
        $findId->execute([$id]);
        $data=$findId->fetch();

        return $data ? Sprint::fromArray($data):null;
    }

    public function findByProjet($projet_id){
        $findProjet = $this->db->prepare("SELECT * FROM sprints WHERE projet_id = ? ORDER BY date_debut DESC");
        $findProjet->execute([$projet_id]);

        $sprints = [];
        while($data=$findProjet->fetch()){
            $sprints[] = Sprint::fromArray($data);
        }
        return $sprints;
    }

    public function update($sprint){
        $sql = "UPDATE sprints SET 
                titre = ?, 
                projet_id = ?, 
                date_debut = ?, 
                date_fin = ?, 
                statut = ?
                WHERE id = ?";

        $update=$this->db->prepare($sql);
        return $update->execute([
            $sprint->getTitre(), 
            $sprint->getProjetId(),
            $sprint->getDateDebut(),
            $sprint->getDateFin(),
            $sprint->getStatut(), 
            $sprint->getId() 
        ]);
    }

    public function delete($id){
        $delete = $this->db->prepare("DELETE FROM sprints WHERE id = ?");
        return $delete->execute([$id]);
    }

    public function deleteByProjet($projet_id) {
        $stmt = $this->db->prepare("DELETE FROM sprints WHERE projet_id = ?");
        return $stmt->execute([$projet_id]);
    }
}

?>