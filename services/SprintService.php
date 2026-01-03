<?php

require_once __DIR__ . '/../entities/Sprint.php';
require_once __DIR__ . '/../repositories/SprintRepository.php';

class SprintService {
    private $sprintRepository;
    
    public function __construct($sprintRepository) {
        $this->sprintRepository = $sprintRepository;
    }

    public function createSprint($data){
        $sprint = new Sprint();
        $sprint->setTitre($data['titre'] ?? '');
        $sprint->setProjetId($data['projet_id'] ?? null);
        $sprint->setDateDebut($data['date_debut'] ?? null);
        $sprint->setDateFin($data['date_fin'] ?? null);
        $sprint->setStatut($data['statut'] ?? 'planifié');

        if (!$sprint->datesValides()) {
            throw new Exception("La date de fin doit être après la date de début");
        }
        return $this->sprintRepository->create($sprint);

    }
    
    public function getSprintById($id) {
        $sprint = $this->sprintRepository->findById($id);
        if (!$sprint) {
            throw new Exception("Sprint non trouvé");
        }
        return $sprint;
    }

    public function getSprintsByProjet($projet_id){
        return $this->sprintRepository->findByProjet($projet_id);
    }

    public function updateSprint($id, $data){
        $sprint = $this->getSprintById($id);

        if (isset($data['titre'])) $sprint->setTitre($data['titre']);
        if (isset($data['date_debut'])) $sprint->setDateDebut($data['date_debut']);
        if (isset($data['date_fin'])) $sprint->setDateFin($data['date_fin']);
        if (isset($data['statut'])) $sprint->setStatut($data['statut']);

        if (!$sprint->datesValides()) {
            throw new Exception("La date de fin doit être après la date de début");
        }
        return $this->sprintRepository->update($sprint);
    }

    public function deleteSprint($id) {
        return $this->sprintRepository->delete($id);
    }

    public function deleteSprintsByProjet($projet_id) {
        return $this->sprintRepository->deleteByProjet($projet_id);
    }

    public function countSprintsByProjet($projet_id) {
        $sprints = $this->sprintRepository->findByProjet($projet_id);
        return count($sprints);
    }

}


?>