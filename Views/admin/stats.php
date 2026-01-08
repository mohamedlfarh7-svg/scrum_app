<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../repositories/TaskRepository.php';
require_once __DIR__ . '/../repositories/ProjectRepository.php';

$db = Database::connect();
$userRepo = new UserRepository($db);
$taskRepo = new TaskRepository($db);
$projectRepo = new ProjetRepository($db);

$users = $userRepo->getAll();
$tasks = $taskRepo->All();
$projects = $projectRepo->All();

$user_count = count($users);
$active_users = count(array_filter($users, fn($u) => $u->getStatut() === 'actif'));
$task_count = count($tasks);
$project_count = count($projects);
$completed_tasks = count(array_filter($tasks, fn($t) => $t->getStatut() === 'terminée'));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Statistiques</title>
</head>
<body>
    <h1>Statistiques</h1>
    
    <h2>Utilisateurs</h2>
    <p>Total: <?php echo $user_count; ?></p>
    <p>Actifs: <?php echo $active_users; ?></p>
    
    <h2>Tâches</h2>
    <p>Total: <?php echo $task_count; ?></p>
    <p>Terminées: <?php echo $completed_tasks; ?></p>
    
    <h2>Projets</h2>
    <p>Total: <?php echo $project_count; ?></p>
    
    <br>
    <a href="users.php">Gestion utilisateurs</a> |
    <a href="../dashboard.php">Retour</a>
</body>
</html>