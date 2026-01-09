<?php
require_once __DIR__ . '/../entities/Task.php';
require_once __DIR__ . '/../repositories/TaskRepository.php';
require_once __DIR__ . '/../repositories/UserTaskRepository.php';

class TaskService{
    private $taskRepository;
    private $taskUserRepository;

    public function __construct($pdo) {
        $this->taskRepository = new TaskRepository($pdo);
        $this->taskUserRepository = new TaskUserRepository($pdo);
    }

    public function createTask($data){
        $task = new Task();
        $task->setTitre($data['titre'] ?? '');
        $task->setDescription($data['description'] ?? '');
        $task->setSprintId($data['sprint_id'] ?? null);
        $task->setPriorite($data['priorite'] ?? 'moyenne');
        $task->setStatut($data['statut'] ?? 'à faire');
        $task->setCreatedBy($data['created_by'] ?? null);
        $task->setDateEcheance($data['date_echeance'] ?? null);
        
        return $this->taskRepository->create($task);
    }

    public function getTaskById($id) {
        $task = $this->taskRepository->find($id);
        if (!$task) {
            throw new Exception("Tâche non trouvée");
        }
        return $task;
    }

    public function getTasksBySprint($sprint_id) {
        return $this->taskRepository->findBySprint($sprint_id);
    }

    public function updateTask($id, $data) {
        $task = $this->getTaskById($id);
        
        if (isset($data['titre'])) $task->setTitre($data['titre']);
        if (isset($data['description'])) $task->setDescription($data['description']);
        if (isset($data['priorite'])) $task->setPriorite($data['priorite']);
        if (isset($data['statut'])) $task->setStatut($data['statut']);
        if (isset($data['date_echeance'])) $task->setDateEcheance($data['date_echeance']);
        
        return $this->taskRepository->update($task);
    }

    public function deleteTask($id) {
        return $this->taskRepository->delete($id);
    }

    public function assignUserToTask($task_id, $user_id, $role = 'collaborateur') {
        $taskUser = new TaskUser();
        $taskUser->setTaskId($task_id);
        $taskUser->setUserId($user_id);
        $taskUser->setRole($role);
        
        return $this->taskUserRepository->assignUser($taskUser);
    }

    public function getTaskAssignments($task_id) {
        return $this->taskUserRepository->findAssignmentsByTask($task_id);
    }

    public function removeUserFromTask($task_id, $user_id) {
        return $this->taskUserRepository->removeAssignment($task_id, $user_id);
    }
}
?>