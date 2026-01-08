<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../services/CommentService.php';

$comment_id = $_GET['id'] ?? 0;
$task_id = $_GET['task_id'] ?? 0;
$sprint_id = $_GET['sprint_id'] ?? 0;

if (!$comment_id || !$task_id || !$sprint_id) {
    header('Location: list.php');
    exit();
}

$db = Database::connect();
$commentService = new CommentService($db);

$comment = $commentService->getComment($comment_id);

if (!$comment || ($comment->getUserId() != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin')) {
    header("Location: comments.php?task_id=$task_id&sprint_id=$sprint_id");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm'])) {
        try {
            $commentService->deleteComment($comment_id);
            $message = "Commentaire supprimé avec succès";
            header("refresh:2;url=comments.php?task_id=$task_id&sprint_id=$sprint_id");
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        header("Location: comments.php?task_id=$task_id&sprint_id=$sprint_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Supprimer Commentaire</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #333; margin-bottom: 20px; }
        .alert { padding: 15px; border-radius: 5px; margin: 20px 0; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .comment-preview { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: left; }
        .btn-group { display: flex; gap: 10px; justify-content: center; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; }
        .btn-delete { background: #f44336; color: white; }
        .btn-cancel { background: #666; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php else: ?>
            <h1>Supprimer Commentaire</h1>
            
            <div class="alert alert-warning">
                Êtes-vous sûr de vouloir supprimer ce commentaire?
            </div>
            
            <div class="comment-preview">
                <p><strong>Auteur:</strong> Utilisateur #<?php echo $comment->getUserId(); ?></p>
                <p><strong>Date:</strong> <?php echo $comment->getCreatedAt(); ?></p>
                <p><strong>Contenu:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($comment->getContent())); ?></p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="btn-group">
                    <button type="submit" name="confirm" value="1" class="btn btn-delete">
                        Oui, supprimer
                    </button>
                    <a href="comments.php?task_id=<?php echo $task_id; ?>&sprint_id=<?php echo $sprint_id; ?>" class="btn btn-cancel">
                        ❌ Annuler
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>