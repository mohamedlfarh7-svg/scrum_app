<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$task_id = $_GET['task_id'] ?? 0;
$sprint_id = $_GET['sprint_id'] ?? 0;

if (!$task_id || !$sprint_id) {
    header('Location: ../sprints/list.php');
    exit();
}

$error = '';
$success = '';

try {
    require_once __DIR__ . '/../../Core/Database.php';
    require_once __DIR__ . '/../../entities/Task.php';
    require_once __DIR__ . '/../../repositories/TaskRepository.php';
    require_once __DIR__ . '/../../services/TaskService.php';
    require_once __DIR__ . '/../../entities/Sprint.php';
    require_once __DIR__ . '/../../repositories/SprintRepository.php';
    require_once __DIR__ . '/../../services/SprintService.php';
    require_once __DIR__ . '/../../entities/UserTask.php';
    require_once __DIR__ . '/../../repositories/UserTaskRepository.php';
    require_once __DIR__ . '/../../services/UserService.php';
    
    $db = Database::connect();
    
    $sprintRepo = new SprintRepository($db);
    $sprintService = new SprintService($sprintRepo);
    $sprint = $sprintService->getSprintById($sprint_id);
    
    if (!$sprint) {
        header('Location: ../sprints/list.php');
        exit();
    }
 
    $taskRepo = new TaskRepository($db);
    $taskService = new TaskService($taskRepo);
    $task = $taskService->getTaskById($task_id);
    
    if (!$task || $task->getSprintId() != $sprint_id) {
        header('Location: list.php?sprint_id=' . $sprint_id);
        exit();
    }

    $taskUserRepo = new TaskUserRepository($db);
    
    $UserService = new UserService($db);
    $users = $UserService->getAllUsers();
    $assignments = $taskUserRepo->findAssignmentsByTask($task_id);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['assign_user'])) {
            $user_id = $_POST['user_id'] ?? 0;
            $role = $_POST['role'] ?? 'assignee';
            
            if ($user_id) {
                $taskUser = new TaskUser();
                $taskUser->setTaskId($task_id);
                $taskUser->setUserId($user_id);
                $taskUser->setRole($role);
                
                $TaskUserRepository->assignUser($taskUser);
                $success = "✅ Utilisateur assigné avec succès!";
                header("refresh:2;url=assign.php?task_id=$task_id&sprint_id=$sprint_id");
            }
        }
        
        if (isset($_POST['remove_assignment'])) {
            $assignment_id = $_POST['assignment_id'] ?? 0;
            
            if ($assignment_id) {
                $TaskUserRepository->removeAssignment($assignment_id);
                $success = "✅ Assignation supprimée avec succès!";
                header("refresh:2;url=assign.php?task_id=$task_id&sprint_id=$sprint_id");
            }
        }
    }
    
} catch (Exception $e) {
    $error = "❌ Erreur: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigner Utilisateurs - <?php echo htmlspecialchars($task->getTitre()); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #f5f7fa;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .back-btn {
            background: #764ba2;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .header {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .task-info h1 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .task-details {
            display: flex;
            gap: 20px;
            margin-top: 15px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .assign-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .card-title {
            color: #333;
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 0.95rem;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            background: #f9f9f9;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #764ba2, #667eea);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, #663399, #5a67d8);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #ddd;
        }
        
        .assignments-list {
            list-style: none;
        }
        
        .assignment-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .assignment-info h4 {
            margin-bottom: 5px;
            color: #333;
        }
        
        .assignment-role {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .role-assignee { background: #d4edda; color: #155724; }
        .role-reviewer { background: #cce5ff; color: #004085; }
        .role-follower { background: #fff3cd; color: #856404; }
        
        .btn-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .assign-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <a href="list.php?sprint_id=<?php echo $sprint_id; ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Retour aux tâches
        </a>
        
        <div class="header">
            <div class="task-info">
                <h1>Assigner des utilisateurs</h1>
                <h2 style="color: #666; font-size: 1.2rem;"><?php echo htmlspecialchars($task->getTitre()); ?></h2>
                
                <div class="task-details">
                    <div class="detail-item">
                        <i class="fas fa-running"></i>
                        Sprint: <?php echo htmlspecialchars($sprint->getTitre()); ?>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-flag"></i>
                        Priorité: <?php echo $task->getPrioriteText(); ?>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-check-circle"></i>
                        Statut: <?php echo $task->getStatutText(); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="assign-section">

            <div class="card">
                <h3 class="card-title">
                    <i class="fas fa-user-plus"></i>
                    Nouvelle assignation
                </h3>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Utilisateur</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">Sélectionner un utilisateur</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['nom']); ?> 
                                    (<?php echo htmlspecialchars($user['role']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Rôle</label>
                        <select name="role" class="form-control">
                            <option value="assignee">Assigné (responsable)</option>
                            <option value="reviewer">Relecteur</option>
                            <option value="follower">Suiveur</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="assign_user" class="btn btn-primary">
                        <i class="fas fa-user-check"></i>
                        Assigner l'utilisateur
                    </button>
                </form>
            </div>

            <div class="card">
                <h3 class="card-title">
                    <i class="fas fa-users"></i>
                    Utilisateurs assignés
                </h3>
                
                <?php if (empty($assignments)): ?>
                    <div class="empty-state">
                        <i class="fas fa-user-friends"></i>
                        <p>Aucun utilisateur assigné</p>
                    </div>
                <?php else: ?>
                    <ul class="assignments-list">
                        <?php foreach ($assignments as $assignment): ?>
                            <li class="assignment-item">
                                <div class="assignment-info">
                                    <h4>Utilisateur #<?php echo $assignment->getUserId(); ?></h4>
                                    <span class="assignment-role role-<?php echo $assignment->getRole(); ?>">
                                        <?php echo $assignment->getRoleText(); ?>
                                    </span>
                                    <div style="font-size: 0.85rem; color: #888; margin-top: 5px;">
                                        Assigné le: <?php echo $assignment->getDateAssignation(); ?>
                                    </div>
                                </div>
                                
                                <form method="POST" action="" style="margin: 0;">
                                    <input type="hidden" name="assignment_id" value="<?php echo $assignment->getId(); ?>">
                                    <button type="submit" name="remove_assignment" class="btn btn-danger" 
                                            onclick="return confirm('Retirer cet utilisateur?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="list.php?sprint_id=<?php echo $sprint_id; ?>" class="btn btn-secondary">
                <i class="fas fa-tasks"></i>
                Retour aux tâches
            </a>
        </div>
    </div>
</body>
</html>