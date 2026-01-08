<?php
require_once __DIR__ . '/../entities/Notification.php';

class NotificationRepository {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function create(Notification $notification) {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (user_id, type, message, related_id, is_read, created_at) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $notification->getUserId(),
            $notification->getType(),
            $notification->getMessage(),
            $notification->getRelatedId(),
            $notification->getIsRead() ? 1 : 0,
            $notification->getCreatedAt()
        ]);
    }
    
    public function findByUser($user_id, $limit = 50, $only_unread = false) {
        $sql = "
            SELECT * FROM notifications 
            WHERE user_id = ? 
        ";
        
        if ($only_unread) {
            $sql .= " AND is_read = 0 ";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id, $limit]);
        
        $notifications = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $notifications[] = new Notification(
                $data['user_id'],
                $data['type'],
                $data['message'],
                $data['related_id'],
                $data['id'],
                $data['is_read'] == 1,
                $data['created_at']
            );
        }
        return $notifications;
    }
    
    public function markAsRead($id) {
        $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function markAllAsRead($user_id) {
        $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
        return $stmt->execute([$user_id]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM notifications WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function deleteOld($days = 30) {
        $stmt = $this->pdo->prepare("DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
        return $stmt->execute([$days]);
    }
    
    public function countUnread($user_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
}
?>