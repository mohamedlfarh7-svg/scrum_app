<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$sprint_id = $_GET['sprint_id'] ?? 0;
if (!$sprint_id) {
    header('Location: ../sprints/list.php');
    exit();
}

try {
    require_once __DIR__ . '/../../Core/Database.php';
    require_once __DIR__ . '/../../entities/Task.php';
    require_once __DIR__ . '/../../repositories/TaskRepository.php';
    require_once __DIR__ . '/../../services/TaskService.php';
    require_once __DIR__ . '/../../entities/Sprint.php';
    require_once __DIR__ . '/../../repositories/SprintRepository.php';
    require_once __DIR__ . '/../../services/SprintService.php';
    
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
    $tasks = $taskService->getTasksBySprint($sprint_id);
    
} catch (Exception $e) {
    die("Erreur: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tâches - <?php echo htmlspecialchars($sprint->getTitre()); ?></title>
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
        
        .sprint-info h1 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .sprint-details {
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
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #764ba2;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .tasks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .task-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        
        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .task-title {
            color: #333;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .task-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-a_faire { background: #e3f2fd; color: #1565c0; }
        .status-en_cours { background: #e8f5e9; color: #2e7d32; }
        .status-terminee { background: #f3e5f5; color: #7b1fa2; }
        .status-bloquee { background: #ffebee; color: #c62828; }
        
        .task-priority {
            background: #f8f9ff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .priority-basse { color: #2ecc71; }
        .priority-moyenne { color: #f39c12; }
        .priority-haute { color: #e74c3c; }
        .priority-critique { color: #c0392b; font-weight: bold; }
        
        .task-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .task-dates {
            color: #888;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .task-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-edit {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .btn-delete {
            background: #ffebee;
            color: #d32f2f;
        }
        
        .btn-assign {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .btn-create {
            background: linear-gradient(to right, #764ba2, #667eea);
            color: white;
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <a href="../sprints/list.php?id=<?php echo $sprint->getProjetId(); ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Retour aux sprints
        </a>
        
        <div class="header">
            <div class="sprint-info">
                <h1><?php echo htmlspecialchars($sprint->getTitre()); ?></h1>
                <span class="task-status status-<?php echo $sprint->getStatut(); ?>">
                    <?php echo $sprint->getStatutText(); ?>
                </span>
                
                <div class="sprint-details">
                    <div class="detail-item">
                        <i class="fas fa-calendar-alt"></i>
                        <?php echo $sprint->getDateDebut(); ?> - <?php echo $sprint->getDateFin(); ?>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-clock"></i>
                        <?php echo $sprint->getDuree(); ?> jours
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($tasks); ?></div>
                <div class="stat-label">Tâches totales</div>
            </div>
            <?php
            $a_faire = 0; $en_cours = 0; $terminee = 0; $bloquee = 0;
            foreach ($tasks as $task) {
                switch ($task->getStatut()) {
                    case 'à faire': $a_faire++; break;
                    case 'en cours': $en_cours++; break;
                    case 'terminée': $terminee++; break;
                    case 'bloquée': $bloquee++; break;
                }
            }
            ?>
            <div class="stat-card">
                <div class="stat-number"><?php echo $a_faire; ?></div>
                <div class="stat-label">À faire</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $en_cours; ?></div>
                <div class="stat-label">En cours</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $terminee; ?></div>
                <div class="stat-label">Terminées</div>
            </div>
        </div>
        
        <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <i class="fas fa-tasks"></i>
                <h3>Aucune tâche trouvée</h3>
                <p>Commencez par créer votre première tâche</p>
                <a href="create.php?sprint_id=<?php echo $sprint_id; ?>" class="btn btn-create">
                    <i class="fas fa-plus"></i>
                    Créer une tâche
                </a>
            </div>
        <?php else: ?>
            <div class="tasks-grid">
                <?php foreach ($tasks as $task): ?>
                    <div class="task-card">
                        <div class="task-header">
                            <h3 class="task-title"><?php echo htmlspecialchars($task->getTitre()); ?></h3>
                            <span class="task-status status-<?php echo str_replace(' ', '_', $task->getStatut()); ?>">
                                <?php echo $task->getStatutText(); ?>
                            </span>
                        </div>
                        
                        <div class="task-priority priority-<?php echo $task->getPriorite(); ?>">
                            <i class="fas fa-flag"></i>
                            <?php echo $task->getPrioriteText(); ?>
                        </div>
                        
                        <?php if ($task->getDescription()): ?>
                            <div class="task-description">
                                <?php echo nl2br(htmlspecialchars($task->getDescription())); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="task-dates">
                            <i class="fas fa-calendar"></i>
                            <?php if ($task->getDateEcheance()): ?>
                                Échéance: <?php echo $task->getDateEcheance(); ?>
                            <?php else: ?>
                                Pas d'échéance
                            <?php endif; ?>
                        </div>
                        
                        <div class="task-actions">
                            <a href="edit.php?id=<?php echo $task->getId(); ?>&sprint_id=<?php echo $sprint_id; ?>" class="btn btn-edit">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </a>
                            <a href="delete.php?id=<?php echo $task->getId(); ?>&sprint_id=<?php echo $sprint_id; ?>" 
                               class="btn btn-delete"
                               onclick="return confirm('Supprimer cette tâche?')">
                                <i class="fas fa-trash"></i>
                                Supprimer
                            </a>
                            <a href="assign.php?task_id=<?php echo $task->getId(); ?>&sprint_id=<?php echo $sprint_id; ?>" class="btn btn-assign">
                                <i class="fas fa-user-plus"></i>
                                Assigner
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center;">
                <a href="create.php?sprint_id=<?php echo $sprint_id; ?>" class="btn btn-create">
                    <i class="fas fa-plus"></i>
                    Créer une nouvelle tâche
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>