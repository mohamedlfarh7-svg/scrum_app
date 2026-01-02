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
            max-width: 87%;
            margin: 0 auto;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
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
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .role-admin {
            background: #e74c3c;
            color: white;
        }
        
        .role-chef {
            background: #3498db;
            color: white;
        }
        
        .role-membre {
            background: #2ecc71;
            color: white;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            background: #c0392b;
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
        
        .hidden {
            display: none;
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
                grid-template-columns: 1fr;
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
                    <?php
                    $role_class = '';
                    switch($user['role']) {
                        case 'admin': $role_class = 'role-admin'; break;
                        case 'chef_projet': $role_class = 'role-chef'; break;
                        default: $role_class = 'role-membre';
                    }
                    ?>
                    <span class="user-role <?php echo $role_class; ?>">
                        <?php echo htmlspecialchars($user['role']); ?>
                    </span>
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
                    <a href="../projects/list.php" class="see-all">Voir tous</a>
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
                </ul>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-tasks"></i> Mes Tâches</h2>
                    <a href="../tasks/list.php" class="see-all">Voir toutes</a>
                </div>
                <ul class="task-list">
                    <li class="task-item">
                        <strong>Design Homepage</strong>
                        <p>Projet: Site E-commerce | À faire</p>
                    </li>
                    <li class="task-item">
                        <strong>API Authentication</strong>
                        <p>Projet: API Rest | En cours</p>
                    </li>
                </ul>
            </div>
        </div>

        <div class="nav-grid">
            <?php if($user['role'] == 'admin'): ?>
                <div class="nav-card" onclick="window.location.href='../admin/users.php'">
                    <i class="fas fa-user-cog nav-icon"></i>
                    <h3>Gestion Utilisateurs</h3>
                    <p>Administrer les comptes</p>
                </div>
            <?php endif; ?>
            
            <?php if(in_array($user['role'], ['admin', 'chef_projet'])): ?>
                <div class="nav-card" onclick="window.location.href='../projects/create.php'">
                    <i class="fas fa-plus-circle nav-icon"></i>
                    <h3>Créer Projet</h3>
                    <p>Nouveau projet Agile</p>
                </div>
            <?php endif; ?>
            
            <div class="nav-card" onclick="window.location.href='../tasks/list.php'">
                <i class="fas fa-tasks nav-icon"></i>
                <h3>Mes Tâches</h3>
                <p>Voir toutes mes tâches</p>
            </div>
            
            <div class="nav-card" onclick="window.location.href='../projects/list.php'">
                <i class="fas fa-project-diagram nav-icon"></i>
                <h3>Mes Projets</h3>
                <p>Projets assignés</p>
            </div>
            
            <div class="nav-card" onclick="window.location.href='../sprints/list.php'">
                <i class="fas fa-running nav-icon"></i>
                <h3>Sprints</h3>
                <p>Voir les sprints</p>
            </div>
            
            <div class="nav-card" onclick="window.location.href='../user/profile.php'">
                <i class="fas fa-user-edit nav-icon"></i>
                <h3>Mon Profil</h3>
                <p>Modifier mes infos</p>
            </div>
        </div>

    </div>
</body>
</html>