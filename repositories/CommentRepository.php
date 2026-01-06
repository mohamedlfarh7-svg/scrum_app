<?php
require_once __DIR__ . '/../entities/Comment.php';

class CommentRepository {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) return null;
        
        return new Comment(
            $data['task_id'],
            $data['user_id'],
            $data['content'],
            $data['parent_comment_id'],
            $data['id'],
            $data['created_at'],
            $data['updated_at']
        );
    }
    
    public function create(Comment $comment) {
        $stmt = $this->pdo->prepare("
            INSERT INTO comments (task_id, user_id, content, parent_comment_id, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $success = $stmt->execute([
            $comment->getTaskId(),
            $comment->getUserId(),
            $comment->getContent(),
            $comment->getParentCommentId()
        ]);
        
        return $success;
    }
    
    public function findByTask($task_id) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.nom as user_name 
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            WHERE c.task_id = ? 
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$task_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findReplies($parent_comment_id) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.nom as user_name 
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            WHERE c.parent_comment_id = ? 
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$parent_comment_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function updateContent($id, $content) {
        $stmt = $this->pdo->prepare("
            UPDATE comments 
            SET content = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$content, $id]);
    }
    
    public function countByTask($task_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM comments WHERE task_id = ?");
        $stmt->execute([$task_id]);
        return $stmt->fetchColumn();
    }
    
    public function findByUser($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>