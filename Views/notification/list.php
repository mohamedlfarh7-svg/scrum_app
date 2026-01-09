<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../../Core/Database.php';
require_once __DIR__ . '/../../services/NotificationService.php';

$db = Database::connect();
$notificationService = new NotificationService($db);

$user_id = $_SESSION['user_id'];
$notifications = $notificationService->getNotifications($user_id);
$unread_count = $notificationService->getUnreadCount($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_all_read'])) {
        $notificationService->markAllAsRead($user_id);
        header("Location: list.php");
        exit();
    }
    
    if (isset($_POST['mark_read'])) {
        $notificationService->markNotificationAsRead($_POST['notification_id']);
        header("Location: list.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin: 0; }
        .badge { background: #ff4444; color: white; border-radius: 50%; padding: 2px 8px; font-size: 0.9em; margin-left: 10px; }
        .btn { padding: 8px 15px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .notifications-list { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .notification { padding: 15px; border-bottom: 1px solid #eee; }
        .notification.unread { background: #f0f8ff; }
        .notification-header { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .notification-type { font-size: 0.8em; color: #666; }
        .notification-date { font-size: 0.8em; color: #999; }
        .notification-message { color: #333; }
        .empty-state { text-align: center; padding: 40px; color: #999; }
        .actions { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                Notifications 
                <?php if ($unread_count > 0): ?>
                    <span class="badge"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </h1>
        </div>
        
        <div class="actions">
            <form method="POST" style="display: inline;">
                <button type="submit" name="mark_all_read" class="btn">Marquer tout comme lu</button>
            </form>
            <a href="../dashboard/dashboard.php" class="btn" style="background: #666;">Tableau de bord</a>
        </div>
        
        <div class="notifications-list">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <p>Aucune notification</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                <div class="notification <?php echo !$notification->getIsRead() ? 'unread' : ''; ?>">
                    <div class="notification-header">
                        <span class="notification-type">
                            <?php 
                            $type = $notification->getType();
                            $types = [
                                'task_assigned' => 'âche assignée',
                                'comment_added' => 'Nouveau commentaire'
                            ];
                            echo $types[$type] ?? $type;
                            ?>
                        </span>
                        <span class="notification-date">
                            <?php echo date('d/m/Y H:i', strtotime($notification->getCreatedAt())); ?>
                        </span>
                    </div>
                    <div class="notification-message">
                        <?php echo htmlspecialchars($notification->getMessage()); ?>
                    </div>
                    
                    <?php if (!$notification->getIsRead()): ?>
                    <div style="margin-top: 10px;">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="notification_id" value="<?php echo $notification->getId(); ?>">
                            <button type="submit" name="mark_read" style="background: #2196F3; padding: 3px 8px; font-size: 0.8em;">
                                Marquer comme lu
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>