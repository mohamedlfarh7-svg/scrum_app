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

if (!$comment || $comment->getUserId() != $_SESSION['user_id']) {
    header("Location: comments.php?task_id=$task_id&sprint_id=$sprint_id");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $content = trim($_POST['content']);
        $commentService->updateComment($comment_id, $content);
        $message = "Commentaire modifié avec succès";
        header("refresh:2;url=comments.php?task_id=$task_id&sprint_id=$sprint_id");
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Modifier Commentaire</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .alert { padding: 10px; border-radius: 5px; margin: 10px 0; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        textarea { width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 1em; margin-bottom: 20px; }
        .btn-group { display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-save { background: #4CAF50; color: white; }
        .btn-cancel { background: #f44336; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modifier Commentaire</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <textarea name="content" rows="6" required><?php echo htmlspecialchars($comment->getContent()); ?></textarea>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-save">Enregistrer</button>
                <a href="comments.php?task_id=<?php echo $task_id; ?>&sprint_id=<?php echo $sprint_id; ?>" class="btn btn-cancel">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</body>
</html>