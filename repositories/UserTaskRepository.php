<?php
require_once __DIR__ . '/../entities/UserTask.php';


class TaskUserRepository {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    public function assignUser($taskUser) {
        $sql = "INSERT INTO user_task (task_id, user_id, role) VALUES (?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $taskUser->getTaskId(),
            $taskUser->getUserId(),
            $taskUser->getRole()
        ]);

        $taskUser->setId($this->db->lastInsertId());
        return $taskUser;
    }

    public function findAssignmentsByTask($task_id) {
        $stmt = $this->db->prepare("SELECT * FROM user_task WHERE task_id = ?");
        $stmt->execute([$task_id]);
        
        $assignments = [];
        while ($data = $stmt->fetch()) {
            $assignments[] = TaskUser::fromArray($data);
        }
        
        return $assignments;
    }

    public function removeAssignment($assignment_id)
    {
        $sql = "DELETE FROM user_tasks WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$assignment_id]);
    }
}
?>