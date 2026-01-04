<?php
require_once __DIR__ . '/../entities/UserTask.php';


class TaskUserRepository {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    public function assignUser($taskUser) {
        $sql = "INSERT INTO task_user (task_id, user_id, role) VALUES (?, ?, ?)";
        
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
        $stmt = $this->db->prepare("SELECT * FROM task_user WHERE task_id = ?");
        $stmt->execute([$task_id]);
        
        $assignments = [];
        while ($data = $stmt->fetch()) {
            $assignments[] = TaskUser::fromArray($data);
        }
        
        return $assignments;
    }

    public function removeAssignment($task_id, $user_id) {
        $stmt = $this->db->prepare("DELETE FROM task_user WHERE task_id = ? AND user_id = ?");
        return $stmt->execute([$task_id, $user_id]);
    }
}
?>