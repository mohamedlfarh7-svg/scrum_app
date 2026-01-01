<?php 

class projet{
    private $id;
    private $titre;
    private $description;
    private $chef_id;
    private $date_creation;
    private $statut;

    public function __construct(       
        $id = null,
        $titre = '',
        $description = '',
        $chef_id = null,
        $date_creation = null,
        $statut = 'en_attente'
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->chef_id = $chef_id;
        $this->date_creation = $date_creation ?? date('Y-m-d H:i:s');
        $this->statut = $statut;
    }


    public function getId() {
        return $this->id;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getChefId() {
        return $this->chef_id;
    }

    public function getDateCreation() {
        return $this->date_creation;
    }

    public function getDateCreationFormatted($format = 'Y-m-d H:i:s') {
        return date($format, strtotime($this->date_creation));
    }

    public function getStatut() {
        return $this->statut; 
    }

    public function getStatutText() {
        $statuts = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'suspendu' => 'Suspendu',
            'annule' => 'Annulé'
        ];
        return $statuts[$this->statut] ?? $this->statut;
    }


    public function setId($id) {
        $this->id = (int)$id;
        return $this;
    }

    public function setTitre($titre) {
        if(empty(trim($titre))) {
            throw new InvalidArgumentException("Le titre du projet ne peut pas être vide");
        }
        $this->titre = trim($titre);
        return $this;
    }

    public function setDescription($description) {
        $this->description = trim($description);
        return $this;
    }

    public function setChefId($chef_id) {
        $this->chef_id = $chef_id ? (int)$chef_id : null;
        return $this;
    }

    public function setDateCreation($date_creation) {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function setStatut($statut) {
        $statuts_valides = ['en_attente', 'en_cours', 'termine', 'suspendu', 'annule'];
        if(!in_array($statut, $statuts_valides)) {
            throw new InvalidArgumentException("Le statut n'est pas valide");
        }
        $this->statut = $statut;
        return $this;
    }


    public static function fromArray(array $data) {
        $projet = new projet();
        
        if (isset($data['id'])) $projet->setId($data['id']);
        if (isset($data['titre'])) $projet->setTitre($data['titre']);
        if (isset($data['description'])) $projet->setDescription($data['description']);
        if (isset($data['chef_id'])) $projet->setChefId($data['chef_id']);
        if (isset($data['date_creation'])) $projet->setDateCreation($data['date_creation']);
        if (isset($data['statut'])) $projet->setStatut($data['statut']);
        
        return $projet;
    }


    public function toArray() {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'chef_id' => $this->chef_id,
            'date_creation' => $this->date_creation,
            'statut' => $this->statut,
            'statut_text' => $this->getStatutText(),
            'date_creation_formatted' => $this->getDateCreationFormatted()
        ];
    }


}

?>