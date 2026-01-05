<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

try {
    require_once __DIR__ . '/../../Core/Database.php';
    require_once __DIR__ . '/../../entities/Project.php';
    require_once __DIR__ . '/../../repositories/ProjectRepository.php';
    require_once __DIR__ . '/../../services/ProjectService.php';
    
    $db = Database::connect();
    $repo = new ProjetRepository($db);
    $service = new ProjetService($repo);
    
    $projets = $service->getAllProjets();
    
    if (!is_array($projets)) {
        $projets = [];
    }
    
} catch (Exception $e) {
    die("Erreur: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Projets</title>
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
        
        .header {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #333;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header h1 i {
            color: #764ba2;
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
        .btn-create {
            background: linear-gradient(to right, #764ba2, #667eea);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #764ba2;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .projects-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: linear-gradient(to right, #764ba2, #667eea);
            color: white;
        }
        
        th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s ease;
        }
        
        tbody tr:hover {
            background: #f8f9ff;
        }
        
        td {
            padding: 15px;
            color: #444;
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .badge-en_attente {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-en_cours {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-termine {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-suspendu {
            background: #f8f9fa;
            color: #6c757d;
        }
        
        .badge-annule {
            background: #f8d7da;
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-edit {
            background: #ffeaa7;
            color: #f39c12;
        }
        
        .btn-delete {
            background: #fab1a0;
            color: #e74c3c;
        }
        
        .btn-view {
            background: #a29bfe;
            color: #6c5ce7;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 15px;
        }
        
        .empty-state h3 {
            margin-bottom: 10px;
            color: #888;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .stats {
                grid-template-columns: 1fr;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            th, td {
                white-space: nowrap;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <a href="../dashboard/dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Retour aux Dashboard
        </a>
        <div class="header">
            <h1>
                <i class="fas fa-project-diagram"></i>
                Liste des Projets
            </h1>
            <a href="create.php" class="btn-create">
                <i class="fas fa-plus"></i>
                Nouveau Projet
            </a>
        </div>
        
        <div class="stats">
            <?php
            $total = count($projets);
            $en_cours = 0;
            $termine = 0;
            $en_attente = 0;
            
            foreach ($projets as $projet) {
                switch ($projet->getStatut()) {
                    case 'en_cours': $en_cours++; break;
                    case 'termine': $termine++; break;
                    case 'en_attente': $en_attente++; break;
                }
            }

            ?>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total Projets</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $en_cours; ?></div>
                <div class="stat-label">En cours</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $termine; ?></div>
                <div class="stat-label">Terminés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $en_attente; ?></div>
                <div class="stat-label">En attente</div>
            </div>
        </div>
        
        <div class="projects-table">
            <?php if (empty($projets)): ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Aucun projet trouvé</h3>
                    <p>Commencez par créer votre premier projet</p>
                    <a href="create.php" class="btn-create" style="margin-top: 20px; display: inline-block;">
                        <i class="fas fa-plus"></i>
                        Créer un projet
                    </a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Chef ID</th>
                            <th>Date Création</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projets as $projet): ?>
                            <?php 
                            $badgeClass = 'badge-' . $projet->getStatut();
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($projet->getId()); ?></td>
                                <td><?php echo htmlspecialchars($projet->getTitre()); ?></td>
                                <td>
                                    <?php 
                                    $description = htmlspecialchars($projet->getDescription());
                                    echo strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                                    ?>
                                </td>
                                <td><?php echo $projet->getChefId() ?: '-'; ?></td>
                                <td><?php echo $projet->getDateCreationFormatted('d/m/Y'); ?></td>
                                <td>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo $projet->getStatutText(); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit.php?id=<?php echo $projet->getId(); ?>" 
                                           class="btn-action btn-edit" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $projet->getId(); ?>" 
                                           class="btn-action btn-delete" 
                                           title="Supprimer"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="../sprints/list.php?id=<?php echo $projet->getId(); ?>"
                                            class="btn-action btn-sprint" 
                                            title="Voir les sprints">
                                            <i class="fas fa-running"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>