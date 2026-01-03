<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$sprint_id = $_GET['id'] ?? 0;
$projet_id = $_GET['projet_id'] ?? 0;

if (!$sprint_id || !$projet_id) {
    header('Location: ../projects/list.php');
    exit();
}

$error = '';
$success = '';

try {
    require_once __DIR__ . '/../../Core/Database.php';
    require_once __DIR__ . '/../../entities/Sprint.php';
    require_once __DIR__ . '/../../repositories/SprintRepository.php';
    require_once __DIR__ . '/../../services/SprintService.php';
    require_once __DIR__ . '/../../entities/Project.php';
    require_once __DIR__ . '/../../repositories/ProjectRepository.php';
    require_once __DIR__ . '/../../services/ProjectService.php';
    
    $db = Database::connect();
    
    $sprintRepo = new SprintRepository($db);
    $sprintService = new SprintService($sprintRepo);
    
    $projectRepo = new ProjetRepository($db);
    $projectService = new ProjetService($projectRepo);
    
    $projet = $projectService->getProjetById($projet_id);
    $sprint = $sprintService->getSprintById($sprint_id);
    
    if (!$projet || !$sprint || $sprint->getProjetId() != $projet_id) {
        header('Location: list.php?id=' . $projet_id);
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['confirm']) && $_POST['confirm'] === 'oui') {
            $result = $sprintService->deleteSprint($sprint_id);
            
            if ($result) {
                $success = "Sprint supprimé avec succès!";
                header("refresh:2;url=list.php?id=" . $projet_id);
            } else {
                $error = "Erreur lors de la suppression!";
            }
        } else {
            header('Location: list.php?id=' . $projet_id);
            exit();
        }
    }
    
} catch (Exception $e) {
    $error = "Erreur: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer Sprint</title>
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
            max-width: 600px;
            margin: 0 auto;
        }
        
        
        .alert-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .alert-icon {
            font-size: 4rem;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        
        .alert-title {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }
        
        .alert-message {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .sprint-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        
        .sprint-info strong {
            color: #333;
            display: inline-block;
            width: 120px;
        }
        
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        
        .warning-box i {
            margin-right: 10px;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex: 1;
            text-decoration: none;
        }
        
        .btn-danger {
            background: linear-gradient(to right, #e74c3c, #c0392b);
            color: white;
        }
        
        .btn-danger:hover {
            background: linear-gradient(to right, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(231, 76, 60, 0.3);
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #ddd;
        }
        
        .btn-secondary:hover {
            background: #e9ecef;
            transform: translateY(-2px);
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
        
        @media (max-width: 768px) {
            .alert-card {
                padding: 25px;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        
        <div class="alert-card">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php else: ?>
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                
                <h2 class="alert-title">Supprimer le Sprint</h2>
                
                <p class="alert-message">
                    Êtes-vous sûr de vouloir supprimer ce sprint? Cette action est irréversible.
                </p>
                
                <div class="sprint-info">
                    <div><strong>ID:</strong> <?php echo $sprint->getId(); ?></div>
                    <div><strong>Titre:</strong> <?php echo htmlspecialchars($sprint->getTitre()); ?></div>
                    <div><strong>Projet:</strong> <?php echo htmlspecialchars($projet->getTitre()); ?></div>
                    <div><strong>Statut:</strong> <?php echo $sprint->getStatutText(); ?></div>
                    <div><strong>Dates:</strong> <?php echo $sprint->getDateDebut(); ?> - <?php echo $sprint->getDateFin(); ?></div>
                    <div><strong>Durée:</strong> <?php echo $sprint->getDuree(); ?> jours</div>
                </div>
                
                <div class="warning-box">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Attention:</strong> Toutes les données associées à ce sprint seront supprimées.
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="btn-group">
                        <button type="submit" name="confirm" value="oui" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i>
                            Oui, supprimer
                        </button>
                        <a href="list.php?id=<?php echo $projet_id; ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>