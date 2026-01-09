<?php
require_once __DIR__ . '/../repositories/NotificationRepository.php';
require_once __DIR__ . '/../repositories/UserRepository.php';

class NotificationService {
    private $notificationRepository;
    private $userRepository;
    
    public function __construct($pdo) {
        $this->notificationRepository = new NotificationRepository($pdo);
        $this->userRepository = new UserRepository($pdo);
    }
    
    public function notifyTaskAssigned($task_id, $user_id, $task_title, $assigned_by) {
        $user = $this->userRepository->findById($assigned_by);
        $assigner = $user ? $user->getNom() : 'Un utilisateur';
        
        $message = "$assigner vous a assigné la tâche : $task_title";
        
        $notification = new Notification(
            $user_id,
            'task_assigned',
            $message,
            $task_id
        );
        
        return $this->notificationRepository->create($notification);
    }
    
    public function notifyCommentAdded($task_id, $user_id, $commenter_id, $task_title) {
        $commenter = $this->userRepository->findById($commenter_id);
        $commenter_name = $commenter ? $commenter->getNom() : 'Un utilisateur';
        
        $message = "$commenter_name a commenté la tâche : $task_title";
        
        $notification = new Notification(
            $user_id,
            'comment_added',
            $message,
            $task_id
        );
        
        return $this->notificationRepository->create($notification);
    }
    
    public function getNotifications($user_id, $limit = 50, $only_unread = false) {
        return $this->notificationRepository->findByUser($user_id, $limit, $only_unread);
    }
    
    public function getUnreadCount($user_id) {
        return $this->notificationRepository->countUnread($user_id);
    }
    
    public function markNotificationAsRead($notification_id) {
        return $this->notificationRepository->markAsRead($notification_id);
    }
    
    public function markAllAsRead($user_id) {
        return $this->notificationRepository->markAllAsRead($user_id);
    }
}
?>