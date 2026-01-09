<?php
require_once __DIR__ . '/../repositories/CommentRepository.php';
require_once __DIR__ . '/../services/NotificationService.php';
require_once __DIR__ . '/../repositories/TaskRepository.php';
require_once __DIR__ . '/../repositories/UserRepository.php';

class CommentService {
    private $commentRepository;
    private $notificationService;
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->commentRepository = new CommentRepository($pdo);
        $this->notificationService = new NotificationService($pdo);
    }
    
    public function addComment($task_id, $user_id, $content, $parent_comment_id = null) {
        $content = trim($content);
        
        if (empty($content)) {
            throw new Exception("Le commentaire ne peut pas être vide");
        }
        
        $comment = new Comment($task_id, $user_id, $content, $parent_comment_id);
        $success = $this->commentRepository->create($comment);
        
        if ($success) {
            $this->sendCommentNotifications($task_id, $user_id);
        }
        
        return $success;
    }
    
    private function sendCommentNotifications($task_id, $commenter_id) {
        $taskRepo = new TaskRepository($this->pdo);
        $userRepo = new UserRepository($this->pdo);
        
        $task = $taskRepo->find($task_id);
        if (!$task) return;
        
        $commenter = $userRepo->findById($commenter_id);
        $commenter_name = $commenter ? $commenter->getNom() : 'Un utilisateur';
        
        $task_title = $task->getTitre();
        $task_creator = $task->getCreatedBy();
        
        if ($task_creator && $task_creator != $commenter_id) {
            $this->notificationService->notifyCommentAdded(
                $task_id,
                $task_creator,
                $commenter_id,
                $task_title
            );
        }
        
        $task_comments = $this->commentRepository->findByTask($task_id);
        $commenters = [];
        
        foreach ($task_comments as $comment) {
            $comment_user_id = $comment['user_id'];
            if ($comment_user_id != $commenter_id && $comment_user_id != $task_creator) {
                if (!in_array($comment_user_id, $commenters)) {
                    $commenters[] = $comment_user_id;
                    $this->notificationService->notifyCommentAdded(
                        $task_id,
                        $comment_user_id,
                        $commenter_id,
                        $task_title
                    );
                }
            }
        }
    }
    
    public function getTaskComments($task_id) {
        $comments = $this->commentRepository->findByTask($task_id);
        
        $organized = [];
        $replies = [];
        
        foreach ($comments as $comment) {
            $parent_id = $comment['parent_comment_id'] ?? null;
            
            if (!$parent_id) {
                $comment['replies'] = [];
                $organized[$comment['id']] = $comment;
            } else {
                $replies[] = $comment;
            }
        }
        
        foreach ($replies as $reply) {
            $parent_id = $reply['parent_comment_id'] ?? null;
            if ($parent_id && isset($organized[$parent_id])) {
                $organized[$parent_id]['replies'][] = $reply;
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