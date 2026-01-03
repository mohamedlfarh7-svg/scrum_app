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
    
    $sprint = $sprintService->getSprintById($sprint_id);
    
    if (!$sprint || $sprint->getProjetId() != $projet_id) {
        header('Location: list.php?id=' . $projet_id);
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'titre' => trim($_POST['titre'] ?? ''),
            'date_debut' => $_POST['date_debut'] ?? null,
            'date_fin' => $_POST['date_fin'] ?? null,
            'statut' => $_POST['statut'] ?? 'planifié'
        ];
        
        $result = $sprintService->updateSprint($sprint_id, $data);
        
        if ($result) {
            $success = "Sprint modifié avec succès!";
            header("refresh:2;url=list.php?id=" . $projet_id);
        }
    }
    
} catch (Exception $e) {
    $error = " Erreur: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Sprint - <?php echo htmlspecialchars($projet->getTitre()); ?></title>
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
            max-width: 800px;
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
        
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .form-header {
            margin-bottom: 30px;
        }
        
        .form-title {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .project-info {
            color: #666;
            font-size: 1rem;
            padding-left: 35px;
        }
        
        .sprint-info {
            background: #f8f9ff;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            display: flex;
            gap: 20px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 1rem;
        }
        
        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #764ba2;
            background: white;
            box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.1);
        }
        
        .date-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
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
        
        .btn-primary {
            background: linear-gradient(to right, #764ba2, #667eea);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, #663399, #5a67d8);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
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
            .form-card {
                padding: 25px;
            }
            
            .date-group {
                grid-template-columns: 1fr;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .sprint-info {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <a href="list.php?id=<?php echo $projet_id; ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Retour aux sprints
        </a>
        
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-title">
                    <i class="fas fa-edit"></i>
                    Modifier le sprint
                </h2>
                <div class="project-info">
                    <i class="fas fa-project-diagram"></i>
                    Projet: <strong><?php echo htmlspecialchars($projet->getTitre()); ?></strong>
                </div>
                
                <div class="sprint-info">
                    <div class="info-item">
                        <i class="fas fa-hashtag"></i>
                        <span>ID: <?php echo $sprint->getId(); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Durée: <?php echo $sprint->getDuree(); ?> jours</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-flag"></i>
                        <span>Statut: <?php echo $sprint->getStatutText(); ?></span>
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
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Titre du sprint *</label>
                    <input type="text" 
                           name="titre" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($sprint->getTitre()); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Dates</label>
                    <div class="date-group">
                        <div>
                            <label class="form-label">Date de début</label>
                            <input type="date" 
                                   name="date_debut" 
                                   class="form-control"
                                   value="<?php echo $sprint->getDateDebut(); ?>">
                        </div>
                        <div>
                            <label class="form-label">Date de fin</label>
                            <input type="date" 
                                   name="date_fin" 
                                   class="form-control"
                                   value="<?php echo $sprint->getDateFin(); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-control">
                        <option value="planifié" <?php echo $sprint->getStatut() == 'planifié' ? 'selected' : ''; ?>>Planifié</option>
                        <option value="en_cours" <?php echo $sprint->getStatut() == 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                        <option value="terminé" <?php echo $sprint->getStatut() == 'terminé' ? 'selected' : ''; ?>>Terminé</option>
                        <option value="annulé" <?php echo $sprint->getStatut() == 'annulé' ? 'selected' : ''; ?>>Annulé</option>
                    </select>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Enregistrer
                    </button>
                    <a href="list.php?id=<?php echo $projet_id; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>