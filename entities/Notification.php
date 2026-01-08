<?php
class Notification
{
    private $id;
    private $user_id;
    private $type;
    private $message;
    private $related_id;
    private $is_read;
    private $created_at;

    public function __construct(
        $user_id,
        $type,
        $message,
        $related_id = null,
        $id = null,
        $is_read = false,
        $created_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->type = $type;
        $this->message = $message;
        $this->related_id = $related_id;
        $this->is_read = $is_read;
        $this->created_at = $created_at ?? date('Y-m-d H:i:s');
    }

    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getType() { return $this->type; }
    public function getMessage() { return $this->message; }
    public function getRelatedId() { return $this->related_id; }
    public function getIsRead() { return $this->is_read; }
    public function getCreatedAt() { return $this->created_at; }
    
    public function markAsRead() { 
        $this->is_read = true;
        return $this;
    }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'message' => $this->message,
            'related_id' => $this->related_id,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
            'created_at_formatted' => date('d/m/Y H:i', strtotime($this->created_at))
        ];
    }
}
?>