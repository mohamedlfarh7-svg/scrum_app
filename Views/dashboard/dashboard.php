<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../../Core/Database.php';

class User {
    private $db;
    
    public function __construct()
    {
        $this->db = Database::connect();
    }
    
    public function getUserId($id){
        $get = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $get->execute(['id' => $id]);
        return $get->fetch(PDO::FETCH_ASSOC);
    }
}

$userObj = new User();
$user = $userObj->getUserId($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Agile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .user-details h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .user-role {
            display: inline-block;
            background: #2ecc71;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
            
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f1f1;
        }
        
        .card-header h2 {
            color: #2c3e50;
            font-size: 20px;
        }
        
        .see-all {
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
        }
        
        .project-list, .task-list {
            list-style: none;
        }
        
        .project-item, .task-item {
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #3498db;
        }
        
        .task-item.done {
            border-left-color: #2ecc71;
        }
        
        .task-item.pending {
            border-left-color: #f39c12;
        }
        
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .nav-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .nav-card:hover {
            transform: translateY(-5px);
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
        }
        
        .nav-card:hover i {
            color: white;
        }
        
        .nav-icon {
            font-size: 40px;
            color: #3498db;
            margin-bottom: 15px;
        }
        
        .nav-card h3 {
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .user-profile {
                flex-direction: column;
                text-align: center;
            }
            .dashboard-grid {
            
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));

            
        }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="user-profile">
            <div class="avatar"><?php echo strtoupper(substr($user['nom'], 0, 2)); ?></div>
            <div class="user-details">
                <h1><?php echo htmlspecialchars($user['nom']); ?></h1>
                <span class="user-role"><?php echo htmlspecialchars($user['role']); ?></span>
            </div>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php'">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </button>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number">42</div>
                <div class="stat-label">Utilisateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div class="stat-number">15</div>
                <div class="stat-label">Projets</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-number">87</div>
                <div class="stat-label">Tâches</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-running"></i>
                </div>
                <div class="stat-number">12</div>
                <div class="stat-label">Sprints</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-project-diagram"></i> Mes Projets</h2>
                    <a href="#" class="see-all">Voir tous</a>
                </div>
                <ul class="project-list">
                    <li class="project-item">
                        <strong>Site E-commerce</strong>
                        <p>Progression: 75% | Échéance: 15/12/2024</p>
                    </li>
                    <li class="project-item">
                        <strong>App Mobile</strong>
                        <p>Progression: 45% | Échéance: 30/01/2025</p>
                    </li>
                    <li class="project-item">
                        <strong>API Rest</strong>
                        <p>Progression: 90% | Échéance: 10/12/2024</p>
                    </li>
                </ul>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-tasks"></i> Mes Tâches</h2>
                    <a href="#" class="see-all">Voir toutes</a>
                </div>
                <ul class="task-list">
                    <li class="task-item done">
                        <strong>Design Homepage</strong>
                        <p>Projet: Site E-commerce | Terminée</p>
                    </li>
                    <li class="task-item pending">
                        <strong>API Authentication</strong>
                        <p>Projet: API Rest | En cours</p>
                    </li>
                    <li class="task-item">
                        <strong>Testing Mobile App</strong>
                        <p>Projet: App Mobile | À faire</p>
                    </li>
                </ul>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-calendar-alt"></i> Sprints Actifs</h2>
                    <a href="#" class="see-all">Voir tous</a>
                </div>
                <ul class="project-list">
                    <li class="project-item">
                        <strong>Sprint #12</strong>
                        <p>Projet: Site E-commerce | 01/12 - 15/12</p>
                    </li>
                    <li class="project-item">
                        <strong>Sprint #8</strong>
                        <p>Projet: App Mobile | 10/12 - 24/12</p>
                    </li>
                </ul>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-bell"></i> Notifications</h2>
                    <a href="#" class="see-all">Voir toutes</a>
                </div>
                <ul class="project-list">
                    <li class="project-item">
                        <strong>Nouveau commentaire</strong>
                        <p>Marie a commenté votre tâche</p>
                    </li>
                    <li class="project-item">
                        <strong>Tâche assignée</strong>
                        <p>Nouvelle tâche dans API Rest</p>
                    </li>
                </ul>
            </div>
        </div>

        <div class="nav-grid">
            <div class="nav-card">
                <i class="fas fa-user-cog nav-icon"></i>
                <h3>Gestion Utilisateurs</h3>
                <p>Créer, modifier, administrer</p>
            </div>
            <div class="nav-card">
                <i class="fas fa-plus-circle nav-icon"></i>
                <h3>Créer Projet</h3>
                <p>Nouveau projet Agile</p>
            </div>
            <div class="nav-card">
                <i class="fas fa-search nav-icon"></i>
                <h3>Recherche Avancée</h3>
                <p>Tâches, projets, membres</p>
            </div>
            <div class="nav-card">
                <i class="fas fa-chart-line nav-icon"></i>
                <h3>Statistiques</h3>
                <p>Rapports et analyses</p>
            </div>
        </div>

        <div class="footer">
            <p>© 2024 Gestion de Projets Agile | Dashboard v2.0</p>
        </div>
    </div>

    <script>

        document.querySelectorAll('.nav-card').forEach(card => {
            card.addEventListener('click', function() {
                const title = this.querySelector('h3').textContent;
                alert(`Navigation vers: ${title}`);
            });
        });
        
    </script>
</body>
</html>