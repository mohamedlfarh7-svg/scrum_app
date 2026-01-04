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
    
    $db = Database::connect();
    
    $sprintRepo = new SprintRepository($db);
    $sprintService = new SprintService($sprintRepo);
    $sprint = $sprintService->getSprintById($sprint_id);
    
    if (!$sprint) {
        header('Location: ../sprints/list.php');
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $taskRepo = new TaskRepository($db);
        $taskService = new TaskService($taskRepo);
        
        $data = [
            'titre' => trim($_POST['titre'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'sprint_id' => $sprint_id,
            'priorite' => $_POST['priorite'] ?? 'moyenne',
            'statut' => $_POST['statut'] ?? 'à faire',
            'created_by' => $_SESSION['user_id'],
            'date_echeance' => $_POST['date_echeance'] ?? null
        ];
        
        $task = $taskService->createTask($data);
        
        if ($task) {
            $success = "Tâche créée avec succès!";
            header("refresh:2;url=list.php?sprint_id=" . $sprint_id);
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
    <title>Créer Tâche - <?php echo htmlspecialchars($sprint->getTitre()); ?></title>
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
        
        .sprint-info {
            color: #666;
            font-size: 1rem;
            padding-left: 35px;
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
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
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
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-title">
                    <i class="fas fa-plus"></i>
                    Créer une nouvelle tâche
                </h2>
                <div class="sprint-info">
                    <i class="fas fa-running"></i>
                    Sprint: <strong><?php echo htmlspecialchars($sprint->getTitre()); ?></strong>
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
                    <label class="form-label">Titre de la tâche *</label>
                    <input type="text" 
                           name="titre" 
                           class="form-control" 
                           placeholder="Ex: Développer l'API d'authentification"
                           required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" 
                              class="form-control" 
                              placeholder="Décrivez la tâche..."></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Priorité</label>
                    <select name="priorite" class="form-control">
                        <option value="basse">Basse</option>
                        <option value="moyenne" selected>Moyenne</option>
                        <option value="haute">Haute</option>
                        <option value="critique">Critique</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-control">
                        <option value="à faire" selected>À faire</option>
                        <option value="en cours">En cours</option>
                        <option value="terminée">Terminée</option>
                        <option value="bloquée">Bloquée</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date d'échéance</label>
                    <input type="date" 
                           name="date_echeance" 
                           class="form-control">
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Créer la tâche
                    </button>
                    <a href="list.php?sprint_id=<?php echo $sprint_id; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>