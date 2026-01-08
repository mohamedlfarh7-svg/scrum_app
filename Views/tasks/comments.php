<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../services/CommentService.php';
require_once __DIR__ . '/../services/TaskService.php';

$task_id = $_GET['task_id'] ?? 0;
$sprint_id = $_GET['sprint_id'] ?? 0;

if (!$task_id || !$sprint_id) {
    header('Location: list.php');
    exit();
}

$db = Database::connect();
$commentService = new CommentService($db);
$taskService = new TaskService($db);

$task = $taskService->getTaskById($task_id);
$comments = $commentService->getTaskComments($task_id);
$comment_count = $commentService->getCommentCount($task_id);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    try {
        $content = trim($_POST['content']);
        $parent_comment_id = $_POST['parent_comment_id'] ?? null;
        
        $commentService->addComment($task_id, $_SESSION['user_id'], $content, $parent_comment_id);
        $message = "Commentaire ajouté avec succès";
        header("refresh:2;url=comments.php?task_id=$task_id&sprint_id=$sprint_id");
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Commentaires</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header h1 { color: #333; margin: 0; }
        .task-info { color: #666; margin: 10px 0; }
        .alert { padding: 10px; border-radius: 5px; margin: 10px 0; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .comment-form { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .comment-form textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 10px; }
        .btn { padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .comments-list { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .comment { padding: 15px; border-bottom: 1px solid #eee; }
        .comment-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .comment-author { font-weight: bold; color: #333; }
        .comment-date { color: #999; font-size: 0.9em; }
        .comment-content { color: #333; line-height: 1.5; }
        .reply-form { margin-left: 30px; margin-top: 10px; }
        .reply-form textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        .reply-btn { background: transparent; color: #2196F3; border: none; cursor: pointer; margin-top: 5px; }
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
                        <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></span>
                    </div>
                    <div class="comment-content">
                        <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                    </div>
                    
                    <button class="reply-btn" onclick="showReplyForm(<?php echo $comment['id']; ?>)">
                        Répondre
                    </button>
                    
                    <div id="reply-form-<?php echo $comment['id']; ?>" class="reply-form" style="display: none;">
                        <form method="POST">
                            <input type="hidden" name="parent_comment_id" value="<?php echo $comment['id']; ?>">
                            <textarea name="content" rows="3" placeholder="Votre réponse..." required></textarea>
                            <button type="submit" name="add_comment" class="btn">💬 Répondre</button>
                        </form>
                    </div>
                    
                    <?php if (!empty($comment['replies'])): ?>
                    <div class="replies">
                        <?php foreach ($comment['replies'] as $reply): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <span class="comment-author"><?php echo htmlspecialchars($reply['user_name'] ?? 'Utilisateur'); ?></span>
                                <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($reply['created_at'])); ?></span>
                            </div>
                            <div class="comment-content">
                                <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
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
            ← Retour aux tâches
        </a>
    </div>
    
    <script>
    function showReplyForm(commentId) {
        var form = document.getElementById('reply-form-' + commentId);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
    </script>
</body>
</html>