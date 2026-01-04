<?php
class Task {
    private $id;
    private $titre;
    private $description;
    private $sprint_id;
    private $priorite;
    private $statut;
    private $created_by;
    private $date_creation;
    private $date_echeance;

    public function __construct(
        $id = null,
        $titre = '',
        $description = '',
        $sprint_id = null,
        $priorite ='Moyenne',
        $statut = 'À faire',
        $created_by = null,
        $date_creation = null,
        $date_echeance = null
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->sprint_id = $sprint_id;
        $this->priorite = $priorite;
        $this->statut = $statut;
        $this->created_by = $created_by;
        $this->date_creation = $date_creation ?? date('Y-m-d H:i:s');
        $this->date_echeance = $date_echeance;
    }

    public function getId() 
    { 
        return $this->id; 
    }
    public function getTitre()
    { 
        return $this->titre; 
    }
    public function getDescription() 
    { 
        return $this->description; 
    }
    public function getSprintId() 
    { 
        return $this->sprint_id; 
    }
    public function getPriorite() 
    { 
        return $this->priorite; 
    }
    public function getStatut() 
    { 
        return $this->statut; 
    }
    public function getCreatedBy() 
    { 
        return $this->created_by; 
    }
    public function getDateCreation() 
    { 
        return $this->date_creation; 
    }
    public function getDateEcheance() 
    { 
        return $this->date_echeance; 
    }

    public function getPrioriteText() {
        $priorites = [
            'basse' => 'basse',
            'moyenne' => 'moyenne',
            'haute' => 'haute',
        ];
        return $priorites[$this->priorite] ?? 'Moyenne';
    }

    public function getStatutText() {
        $statuts = [
            'À faire'  => 'À faire',
            'En cours' => 'En cours',
            'Terminée' => 'Terminée',
            'en revision' => 'en revision'
        ];
        return $statuts[$this->statut] ?? 'À faire';
    }

    public function setId($id) 
    { 
        $this->id = (int)$id;
        return $this; 
    }
    public function setTitre($titre) 
    { 
        $this->titre = trim($titre); 
        return $this; 
    }
    public function setDescription($description) 
    { 
        $this->description = trim($description); 
        return $this; 
    }
    public function setSprintId($sprint_id) { 
        $this->sprint_id = (int)$sprint_id; 
        return $this; 
    }
    public function setPriorite($priorite) { 
        $this->priorite = $priorite; 
        return $this; 
    }
    public function setStatut($statut) { 
        $this->statut = $statut;
        return $this; 
    }
    public function setCreatedBy($created_by) { 
        $this->created_by = (int)$created_by;
        return $this; 
    }
    public function setDateCreation($date_creation) {
        $this->date_creation = $date_creation;
        return $this; 
    }
    public function setDateEcheance($date_echeance) {
        $this->date_echeance = $date_echeance;
        return $this; 
    }

    public function isAfaire() { 
        return $this->statut === 'À faire';
    }
    public function isEnCours() { 
        return $this->statut === 'En cours'; 
    }
    public function isTerminee() { 
        return $this->statut === 'Terminée'; 
    }
    public function isBloquee() {
         return $this->statut === 'en revision'; 
    }

    public static function fromArray(array $data) {
        $task = new Task();
        if (isset($data['id'])) $task->setId($data['id']);
        if (isset($data['titre'])) $task->setTitre($data['titre']);
        if (isset($data['description'])) $task->setDescription($data['description']);
        if (isset($data['sprint_id'])) $task->setSprintId($data['sprint_id']);
        if (isset($data['priorite'])) $task->setPriorite($data['priorite']);
        if (isset($data['statut'])) $task->setStatut($data['statut']);
        if (isset($data['created_by'])) $task->setCreatedBy($data['created_by']);
        if (isset($data['date_creation'])) $task->setDateCreation($data['date_creation']);
        if (isset($data['date_echeance'])) $task->setDateEcheance($data['date_echeance']);
        return $task;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'sprint_id' => $this->sprint_id,
            'priorite' => $this->priorite,
            'statut' => $this->statut,
            'created_by' => $this->created_by,
            'date_creation' => $this->date_creation,
            'date_echeance' => $this->date_echeance,
            'priorite_text' => $this->getPrioriteText(),
            'statut_text' => $this->getStatutText()
        ];
    }
}
?>