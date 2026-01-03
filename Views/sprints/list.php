
<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$projet_id = $_GET['id'] ?? 0;
if (!$projet_id) {
    header('Location: ../projects/list.php');
    exit();
}

try {
    require_once __DIR__ . '/../../Core/Database.php';
    require_once __DIR__ . '/../../entities/Project.php';
    require_once __DIR__ . '/../../repositories/ProjectRepository.php';
    require_once __DIR__ . '/../../services/ProjectService.php';
    require_once __DIR__ . '/../../entities/Sprint.php';
    require_once __DIR__ . '/../../repositories/SprintRepository.php';
    require_once __DIR__ . '/../../services/SprintService.php';
    
    $db = Database::connect();
    
    $projectRepo = new ProjetRepository($db);
    $projectService = new ProjetService($projectRepo);
    $projet = $projectService->getProjetById($projet_id);
    
    if (!$projet) {
        header('Location: ../projects/list.php');
        exit();
    }
    
    $sprintRepo = new SprintRepository($db);
    $sprintService = new SprintService($sprintRepo);
    $sprints = $sprintService->getSprintsByProjet($projet_id);
    
} catch (Exception $e) {
    die("Erreur: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sprints - <?php echo htmlspecialchars($projet->getTitre()); ?></title>
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
            max-width: 87%;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
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
        
        .project-info h1 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .project-info .badge {
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .sprints-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .sprint-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        
        .sprint-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .sprint-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .sprint-title {
            color: #333;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .sprint-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-planifié { background: #e3f2fd; color: #1565c0; }
        .status-en_cours { background: #e8f5e9; color: #2e7d32; }
        .status-terminé { background: #f3e5f5; color: #7b1fa2; }
        .status-annulé { background: #ffebee; color: #c62828; }
        
        .sprint-dates {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sprint-dates i {
            color: #764ba2;
        }
        
        .sprint-duration {
            background: #f8f9ff;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #667eea;
            display: inline-block;
        }
        
        .sprint-actions {
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
        <a href="../projects/list.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Retour aux projets
        </a>
        
        <div class="header">
            <div class="project-info">
                <h1><?php echo htmlspecialchars($projet->getTitre()); ?></h1>
                <span class="badge"><?php echo $projet->getStatutText(); ?></span>
                <p style="color: #666; margin-top: 10px;">
                    <i class="fas fa-layer-group"></i>
                    <?php echo count($sprints); ?> sprint(s) trouvé(s)
                </p>
            </div>
        </div>
        
        <?php if (empty($sprints)): ?>
            <div class="empty-state">
                <i class="fas fa-running"></i>
                <h3>Aucun sprint trouvé</h3>
                <p>Commencez par créer votre premier sprint</p>
                <a href="create.php?id=<?php echo $projet_id; ?>" class="btn btn-create">
                    <i class="fas fa-plus"></i>
                    Créer un sprint
                </a>
            </div>
        <?php else: ?>
            <div class="sprints-grid">
                <?php foreach ($sprints as $sprint): ?>
                    <div class="sprint-card">
                        <div class="sprint-header">
                            <h3 class="sprint-title"><?php echo htmlspecialchars($sprint->getTitre()); ?></h3>
                            <span class="sprint-status status-<?php echo $sprint->getStatut(); ?>">
                                <?php echo $sprint->getStatutText(); ?>
                            </span>
                        </div>
                        
                        <div class="sprint-dates">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo $sprint->getDateDebut(); ?> 
                            <i class="fas fa-arrow-right"></i>
                            <?php echo $sprint->getDateFin(); ?>
                        </div>
                        
                        <div class="sprint-duration">
                            <i class="fas fa-clock"></i>
                            <?php echo $sprint->getDuree(); ?> jours
                        </div>
                        
                        <div class="sprint-actions">
                            <a href="edit.php?id=<?php echo $sprint->getId(); ?>&projet_id=<?php echo $projet_id; ?>" class="btn btn-edit">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </a>
                            <a href="delete.php?id=<?php echo $sprint->getId(); ?>&projet_id=<?php echo $projet_id; ?>" 
                               class="btn btn-delete"
                               onclick="return confirm('Supprimer ce sprint?')">
                                <i class="fas fa-trash"></i>
                                Supprimer
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center;">
                <a href="create.php?id=<?php echo $projet_id; ?>" class="btn btn-create">
                    <i class="fas fa-plus"></i>
                    Créer un nouveau sprint
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>