<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$task_id = $_GET['task_id'] ?? 0;
$sprint_id = $_GET['sprint_id'] ?? 0;

if (!$task_id || !$sprint_id) {
    header('Location: list.php');
    exit();
}

try {
    require_once __DIR__ . '/../../Core/Database.php';
    require_once __DIR__ . '/../../services/TaskService.php';
    require_once __DIR__ . '/../../services/CommentService.php';
    require_once __DIR__ . '/../../services/SprintService.php';
    
    $db = Database::connect();
    
    $taskService = new TaskService($db);
    $commentService = new CommentService($db);
    
    require_once __DIR__ . '/../../repositories/SprintRepository.php';
    $sprintRepo = new SprintRepository($db);
    $sprintService = new SprintService($sprintRepo);
    
    $task = $taskService->getTaskById($task_id);
    $sprint = $sprintService->getSprintById($sprint_id);
    
    if (!$task || !$sprint) {
        header('Location: list.php');
        exit();
    }
    
    $comments = $commentService->getTaskComments($task_id);
    $comment_count = $commentService->getCommentCount($task_id);
    
    $message = '';
    $error = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
        $content = trim($_POST['content']);
        $parent_comment_id = $_POST['parent_comment_id'] ?? null;
        
        if ($content) {
            if ($commentService->addComment($task_id, $_SESSION['user_id'], $content, $parent_comment_id)) {
                $message = "Commentaire ajouté";
                header("Location: comments.php?task_id=$task_id&sprint_id=$sprint_id");
                exit();
            } else {
                $error = "Erreur";
            }
        } else {
            $error = "Commentaire vide";
        }
    }
    
} catch (Exception $e) {
    die("Erreur: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commentaires</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin: 0 0 10px 0; }
        .task-info { color: #666; margin: 10px 0; }
        .alert { padding: 10px; border-radius: 5px; margin: 10px 0; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .comment-form { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 10px; }
        .btn { padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .comments-list { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .comment { padding: 15px; border-bottom: 1px solid #eee; }
        .comment-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .comment-author { font-weight: bold; color: #333; }
        .comment-date { color: #999; font-size: 0.9em; }
        .comment-content { color: #333; line-height: 1.5; }
        .reply-btn { background: #2196F3; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; margin-top: 10px; }
        .back-link { display: inline-block; margin-top: 20px; padding: 10px 15px; background: #666; color: white; text-decoration: none; border-radius: 5px; }
        .replies { margin-left: 30px; border-left: 2px solid #eee; padding-left: 15px; }
        .empty-state { text-align: center; padding: 40px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Commentaires</h1>
            <div class="task-info">
                <strong>Tâche:</strong> <?php echo htmlspecialchars($task->getTitre()); ?> 
                | <strong>Sprint:</strong> #<?php echo $sprint_id; ?>
                | <strong>Commentaires:</strong> <?php echo $comment_count; ?>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="comment-form">
            <form method="POST">
                <textarea name="content" rows="4" placeholder="Ajouter un commentaire..." required></textarea>
                <button type="submit" name="add_comment" class="btn">Publier</button>
            </form>
        </div>
        
        <div class="comments-list">
            <h3>Commentaires (<?php echo $comment_count; ?>)</h3>
            
            <?php if (empty($comments)): ?>
                <div class="empty-state">
                    <p>Aucun commentaire pour cette tâche</p>
                    <p>Soyez le premier à commenter!</p>
                </div>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <div class="comment-header">
                        <span class="comment-author"><?php echo htmlspecialchars($comment['user_name'] ?? 'Utilisateur'); ?></span>
                        <span class="comment-date">
                            <?php 
                            $date = $comment['date_creation'] ?? $comment['created_at'] ?? '';
                            if ($date) {
                                echo date('d/m/Y H:i', strtotime($date));
                            }
                            ?>
                        </span>
                    </div>
                    <div class="comment-content">
                        <?php echo nl2br(htmlspecialchars($comment['content'] ?? '')); ?>
                    </div>
                    
                    <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="parent_comment_id" value="<?php echo $comment['id']; ?>">
                        <textarea name="content" rows="2" placeholder="Répondre..." style="width: 100%; padding: 8px; margin-bottom: 5px;"></textarea>
                        <button type="submit" name="add_comment" class="btn" style="padding: 5px 10px; font-size: 0.9em;">
                            Répondre
                        </button>
                    </form>
                    
                    <?php if (!empty($comment['replies'])): ?>
                    <div class="replies">
                        <?php foreach ($comment['replies'] as $reply): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <span class="comment-author"><?php echo htmlspecialchars($reply['user_name'] ?? 'Utilisateur'); ?></span>
                                <span class="comment-date">
                                    <?php 
                                    $reply_date = $reply['date_creation'] ?? $reply['created_at'] ?? '';
                                    if ($reply_date) {
                                        echo date('d/m/Y H:i', strtotime($reply_date));
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="comment-content">
                                <?php echo nl2br(htmlspecialchars($reply['content'] ?? '')); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <a href="list.php?sprint_id=<?php echo $sprint_id; ?>" class="back-link">
            Retour aux tâches
        </a>
    </div>
</body>
</html>