<?php
require_once __DIR__ . '/../entities/Notification.php';

class NotificationRepository {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function create(Notification $notification) {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (user_id, type, message, related_id, is_read, date_creation) 
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
        // Déterminer le nom de la colonne de date
        $date_column = $this->getDateColumnName();
        
        $sql = "SELECT * FROM notifications WHERE user_id = ? ";
        
        if ($only_unread) {
            $sql .= " AND is_read = 0 ";
        }
        
        $sql .= " ORDER BY $date_column DESC LIMIT 50";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        
        $notifications = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $notifications[] = new Notification(
                $data['user_id'],
                $data['type'],
                $data['message'],
                $data['related_id'],
                $data['id'],
                $data['is_read'] == 1,
                $data[$date_column] ?? $data['date_creation'] ?? $data['created_date'] ?? null
            );
        }
        return $notifications;
    }
    
    private function getDateColumnName() {
        // Essayez différents noms de colonnes
        $possible_columns = ['date_creation', 'created_at', 'created_date', 'date', 'timestamp'];
        
        $stmt = $this->pdo->query("SHOW COLUMNS FROM notifications");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($possible_columns as $col) {
            if (in_array($col, $columns)) {
                return $col;
            }
        }
        
        // Par défaut, utiliser 'date_creation'
        return 'date_creation';
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
    
    public function countUnread($user_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$user_id]);
        return (int)$stmt->fetchColumn();
    }
}
?>