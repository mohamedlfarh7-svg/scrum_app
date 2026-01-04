<?php
class TaskUser {
    private $id;
    private $task_id;
    private $user_id;
    private $role;
    private $date_assignation;

    public function __construct(
        $id = null,
        $task_id = null,
        $user_id = null,
        $role = 'collaborateur',
        $date_assignation = null
    ) {
        $this->id = $id;
        $this->task_id = $task_id;
        $this->user_id = $user_id;
        $this->role = $role;
        $this->date_assignation = $date_assignation ?? date('Y-m-d H:i:s');
    }

    public function getId() { return $this->id; }
    public function getTaskId() { return $this->task_id; }
    public function getUserId() { return $this->user_id; }
    public function getRole() { return $this->role; }
    public function getDateAssignation() { return $this->date_assignation; }

    public function getRoleText() {
        $roles = [
            'collaborateur' => 'collaborateur',
            'responsable' => 'responsable',
        ];
        return $roles[$this->role] ?? 'collaborateur';
    }

    public function setId($id) { 
        $this->id = (int)$id; 
        return $this; 
    }
    public function setTaskId($task_id) { 
        $this->task_id = (int)$task_id; 
        return $this; 
    }
    public function setUserId($user_id) { 
        $this->user_id = (int)$user_id; 
        return $this; 
    }
    public function setRole($role) { 
        $this->role = $role; 
        return $this; 
    }
    public function setDateAssignation($date_assignation) { 
        $this->date_assignation = $date_assignation; 
        return $this;
    }

    public static function fromArray(array $data) {
        $taskUser = new TaskUser();
        if (isset($data['id'])) $taskUser->setId($data['id']);
        if (isset($data['task_id'])) $taskUser->setTaskId($data['task_id']);
        if (isset($data['user_id'])) $taskUser->setUserId($data['user_id']);
        if (isset($data['role'])) $taskUser->setRole($data['role']);
        if (isset($data['date_assignation'])) $taskUser->setDateAssignation($data['date_assignation']);
        return $taskUser;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'user_id' => $this->user_id,
            'role' => $this->role,
            'date_assignation' => $this->date_assignation,
            'role_text' => $this->getRoleText()
        ];
    }
}
?>