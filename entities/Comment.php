<?php
class Comment
{
    private $id;
    private $task_id;
    private $user_id;
    private $content;
    private $parent_comment_id;
    private $created_at;
    private $updated_at;

    public function __construct(
        $task_id,
        $user_id,
        $content,
        $parent_comment_id = null,
        $id = null,
        $created_at = null,
        $updated_at = null
    ) {
        $this->id = $id;
        $this->task_id = $task_id;
        $this->user_id = $user_id;
        $this->content = $content;
        $this->parent_comment_id = $parent_comment_id;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId() { return $this->id; }
    public function getTaskId() { return $this->task_id; }
    public function getUserId() { return $this->user_id; }
    public function getContent() { return $this->content; }
    public function getParentCommentId() { return $this->parent_comment_id; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }
}
?>