<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}
        require_once __DIR__ . '/../../Core/Database.php';
        require_once __DIR__ . '/../../entities/Project.php';
        require_once __DIR__ . '/../../repositories/ProjectRepository.php';
        require_once __DIR__ . '/../../services/ProjectService.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        
        $db = Database::connect();
        $repo = new ProjetRepository($db);
        $service = new ProjetService($repo);
        
        $data = [
            'titre' => trim($_POST['titre'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'chef_id' => !empty($_POST['chef_id']) ? (int)$_POST['chef_id'] : null,
            'statut' => $_POST['statut'] ?? 'en_attente'
        ];
        
        $projet = $service->createProjet($data);
        
        $success = " Projet créé avec ID: " . $projet->getId();
        header("refresh:2;url=list.php");
        
    } catch (Exception $e) {
        $error = " Erreur: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer Projet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 800px;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .form-title {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-title i {
            color: #764ba2;
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
            <h2 class="form-title">
                <i class="fas fa-plus-circle"></i>
                Nouveau Projet
            </h2>
            
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
                    <label class="form-label">Titre du projet *</label>
                    <input type="text" 
                           name="titre" 
                           class="form-control" 
                           placeholder="Ex: Développement d'application mobile"
                           required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" 
                              class="form-control" 
                              placeholder="Décrivez votre projet...">
                    </textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Chef de projet (ID)</label>
                    <input type="number" 
                           name="chef_id" 
                           class="form-control" 
                           placeholder="ID du chef de projet">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-control" required>
                        <option value="en_attente" >En attente</option>
                        <option value="en_cours" >En cours</option>
                        <option value="termine">Terminé</option>
                    </select>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Créer
                    </button>
                    <a href="list.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>