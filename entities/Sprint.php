<?php 
class Sprint {
    private $id;
    private $titre;
    private $projet_id;
    private $date_debut;
    private $date_fin;
    private $statut;

    public function __construct(
        $id = null,
        $titre = '',
        $projet_id = null,
        $date_debut = null,
        $date_fin = null,
        $statut = 'planifié'
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->projet_id = $projet_id;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->statut = $statut;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getProjetId() {
        return $this->projet_id;
    }

    public function getDateDebut() {
        return $this->date_debut;
    }

    public function getDateFin() {
        return $this->date_fin;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function getStatutText() {
        $statuts = [
            'planifié' => 'Planifié',
            'en_cours' => 'En cours',
            'terminé' => 'Terminé',
            'annulé' => 'Annulé'
        ];
        return $statuts[$this->statut] ?? 'Planifié';
    }

    public function setId($id) {
        $this->id = (int)$id;
        return $this;
    }

    public function setTitre($titre) {
        $this->titre = trim($titre);
        return $this;
    }

    public function setProjetId($projet_id) {
        $this->projet_id = (int)$projet_id;
        return $this;
    }

    public function setDateDebut($date_debut) {
        $this->date_debut = $date_debut;
        return $this;
    }

    public function setDateFin($date_fin) {
        $this->date_fin = $date_fin;
        return $this;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
        return $this;
    }

    public function getDuree() {
        if (!$this->date_debut || !$this->date_fin) {
            return 0;
        }
        
        $debut = strtotime($this->date_debut);
        $fin = strtotime($this->date_fin);
        $diff = $fin - $debut;
        
        return ceil($diff / (60 * 60 * 24)) + 1;
    }

    public function isEnCours() {
        return $this->statut === 'en_cours';
    }

    public function isTermine() {
        return $this->statut === 'terminé';
    }

    public function isPlanifie() {
        return $this->statut === 'planifié';
    }

    public function isAnnule() {
        return $this->statut === 'annulé';
    }

    public function datesValides() {
        if (!$this->date_debut || !$this->date_fin) {
            return true;
        }
        
        return strtotime($this->date_debut) <= strtotime($this->date_fin);
    }

    public static function fromArray(array $data) {
        $sprint = new Sprint();
        
        if (isset($data['id'])) $sprint->setId($data['id']);
        if (isset($data['titre'])) $sprint->setTitre($data['titre']);
        if (isset($data['projet_id'])) $sprint->setProjetId($data['projet_id']);
        if (isset($data['date_debut'])) $sprint->setDateDebut($data['date_debut']);
        if (isset($data['date_fin'])) $sprint->setDateFin($data['date_fin']);
        if (isset($data['statut'])) $sprint->setStatut($data['statut']);
        
        return $sprint;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'projet_id' => $this->projet_id,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'statut' => $this->statut,
            'statut_text' => $this->getStatutText(),
            'duree' => $this->getDuree()
        ];
    }
}
?>