<?php
class Comment
{
    private $task_id;
    private $user_id;
    private $content;
    private $parent_comment_id;

    public function __construct(
        $task_id,
        $user_id,
        $content,
        $parent_comment_id = null
    ) {
        $this->task_id = $task_id;
        $this->user_id = $user_id;
        $this->content = $content;
        $this->parent_comment_id = $parent_comment_id;
    }

    public function getTaskId() {
        return $this->task_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getContent() {
        return $this->content;
    }

    public function getParentCommentId() {
        return $this->parent_comment_id;
    }
}
?>