<?php

require_once __DIR__ . '/../entities/Task.php';

class TaskRepository {
    private $db;

    public function __construct($db)
    {
        $this->db=$db;
    }

    public function create($task){
        $create=$this->db->prepare("INSERT INTO tasks (titre, description, sprint_id, priorite, statut, created_by, date_echeance) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
        $create->execute([
            $task->getTitre(),
            $task->getDescription(),
            $task->getSprintId(),
            $task->getPriorite(),
            $task->getStatut(),
            $task->getCreatedBy(),
            $task->getDateEcheance()
        ]);
        $task->setId($this->db->lastInsertId());
        return $task;
    }

    public function find($id){
        $find = $this->db->prepare("SELECT * FROM tasks WHERE id = ?");
        $find->execute([$id]);
        $data = $find->fetch();
        return $data ? Task::fromArray($data) : null;
    }

    public function findBySprint($sprint_id){
        $findBySprint = $this->db->prepare("SELECT * FROM tasks WHERE sprint_id = ? ORDER BY date_creation DESC");
        $findBySprint->execute([$sprint_id]);

        $tasks = [];

        while($data=$findBySprint->fetch()){$tasks[] = Task::fromArray($data);}

        return $tasks;
    }

    public function update($task) {
        $sql = "UPDATE tasks SET 
                        titre = ?,description = ?, 
                        sprint_id = ?,priorite = ?,statut = ?, 
                        created_by = ?, date_echeance = ? 
                        WHERE id = ?";
        $update = $this->db->prepare($sql);
        return $update->execute([
            $task->getTitre(),
            $task->getDescription(),
            $task->getSprintId(),
            $task->getPriorite(),
            $task->getStatut(),
            $task->getCreatedBy(),
            $task->getDateEcheance(),
            $task->getId()
        ]);

    }

        public function delete($id) {
        $delete = $this->db->prepare("DELETE FROM tasks WHERE id = ?");
        return $delete->execute([$id]);
    }
}

?>