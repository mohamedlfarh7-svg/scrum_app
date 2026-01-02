<?php
require_once __DIR__ . '/../entities/Project.php'; 
require_once __DIR__ . '/../repositories/ProjectRepository.php';

class ProjetService{

    private $projetRepository;

    public function __construct(ProjetRepository $projetRepository) {
        $this->projetRepository = $projetRepository;
    }
    public function createProjet($data){
        $projet = new projet();
        $projet->setTitre($data['titre']);
        $projet->setDescription($data['description']?? '');
        $projet->setChefId($data['chef_id'] ?? null);
        $projet->setStatut($data['statut'] ?? 'en_attente');

        return $this->projetRepository->create($projet);

    }

    public function getProjetById($id){
        return $this->projetRepository->find($id);
    }

        public function getAllProjets() {
        return $this->projetRepository->all();
    }

    public function updateProjet($id, $data){
        $projet = $this->getProjetById($id);
        if(isset($data['titre'])) $projet->setTitre($data['titre']);
        if (isset($data['description'])) $projet->setDescription($data['description']);
        if (isset($data['chef_id'])) $projet->setChefId($data['chef_id']);
        if (isset($data['statut'])) $projet->setStatut($data['statut']);
        return $this->projetRepository->update($projet);
    }
    public function deleteProjet($id) {
        return $this->projetRepository->delete($id);
    }
}





?>