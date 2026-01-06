 <?php
require_once __DIR__ . '/../repositories/CommentRepository.php';

class CommentService {
    private $commentRepository;
    
    public function __construct($pdo) {
        $this->commentRepository = new CommentRepository($pdo);
    }
    
    public function addComment($task_id, $user_id, $content, $parent_comment_id = null) {
        $content = trim($content);
        
        if (empty($content)) {
            throw new Exception("Le commentaire ne peut pas être vide");
        }
        
        if (strlen($content) > 1000) {
            throw new Exception("Le commentaire ne peut pas dépasser 1000 caractères");
        }
        
        $comment = new Comment($task_id, $user_id, $content, $parent_comment_id);
        return $this->commentRepository->create($comment);
    }
    
    public function getTaskComments($task_id) {
        $comments = $this->commentRepository->findByTask($task_id);
        $organized = [];
        
        foreach ($comments as $comment) {
            if ($comment['parent_comment_id'] === null) {
                $comment['replies'] = [];
                $organized[$comment['id']] = $comment;
            }
        }
        
        foreach ($comments as $comment) {
            if ($comment['parent_comment_id'] !== null && isset($organized[$comment['parent_comment_id']])) {
                $organized[$comment['parent_comment_id']]['replies'][] = $comment;
            }
        }
        
        return array_values($organized);
    }
    
    public function updateComment($id, $content) {
        $content = trim($content);
        
        if (empty($content)) {
            throw new Exception("Le commentaire ne peut pas être vide");
        }
        
        return $this->commentRepository->updateContent($id, $content);
    }
    
    public function deleteComment($id) {
        return $this->commentRepository->delete($id);
    }
    
    public function getCommentCount($task_id) {
        return $this->commentRepository->countByTask($task_id);
    }
    
    public function getComment($id) {
        return $this->commentRepository->find($id);
    }
}
?>