<?php


session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$error = '';
$success = '';
$current_password = '';
$new_password = '';
$confirm_password = '';

require_once __DIR__ . '/../../services/UserService.php';
require_once __DIR__ . '/../../repositories/UserRepository.php';
$userService = new UserService();
$user = $userService->getUserById($_SESSION['user_id']);

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])){
    $nom=$_POST['nom'];
    $email = $_POST['email'];

    try{
        $userService->updateUserProfile($_SESSION['user_id'],$nom,$email);
        $success = "Profil mis à jour avec succès";
        $user=$userService->getUserById($_SESSION['user_id']);
    }catch(Exception $e){
        $error=$e->getMessage();
    }
}


if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password']))
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas";
    } else {
        try{
            $userService->changePassword($_SESSION['user_id'], $current_password, $new_password);
            $success = "Mot de passe changé avec succès";
        }catch(Exception $e){
        
        }
    }

?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
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
            width: 87%;
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

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
        }



        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }


        .header h1 {
            color: white;
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .profile-sidebar {
            text-align: center;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: bold;
        }
        
        .profile-info h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .profile-role {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .role-admin {
            background: #e74c3c;
            color: white;
        }
        
        .role-chef {
            background: #3498db;
            color: white;
        }
        
        .role-member {
            background: #2ecc71;
            color: white;
        }
        
        .profile-stats {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #7f8c8d;
        }
        
        .stat-value {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #6a11cb;
        }
        
        .submit-btn {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .submit-btn:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb);
        }
        
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c62828;
        }
        
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2e7d32;
        }
        
        .section-title {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f1f1;
        }
        
        .password-form {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="../dashboard/dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Retour au Dashboard
            </a>
            <h1>Mon Profil</h1>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-grid">

            <div class="card profile-sidebar">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user->getNom(), 0, 2)); ?>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($user->getNom()); ?></h2>
                    <?php
                    $role_class = '';
                    switch($user->getRole()) {
                        case 'admin': $role_class = 'role-admin'; break;
                        case 'chef_projet': $role_class = 'role-chef'; break;
                        default: $role_class = 'role-member';
                    }
                    ?>
                    <div class="profile-role <?php echo $role_class; ?>">
                        <?php echo htmlspecialchars($user->getRole()); ?>
                    </div>
                    <p><?php echo htmlspecialchars($user->getEmail()); ?></p>
                </div>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <span>Statut:</span>
                        <span class="stat-value"><?php echo htmlspecialchars($user->getStatut()); ?></span>
                    </div>
                </div>
            </div>

            
            <div class="card">
                <h2 class="section-title">Modifier mes informations</h2>
                
                <form method="POST" action="">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="form-group">
                        <label for="nom">Nom complet:</label>
                        <input type="text" id="nom" name="nom" required 
                               value="<?php echo htmlspecialchars($user->getNom()); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($user->getEmail()); ?>">
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </form>

                <div class="password-form">
                    <h2 class="section-title">Changer mon mot de passe</h2>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="change_password" value="1">
                        
                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel:</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Nouveau mot de passe:</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmer le nouveau mot de passe:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-key"></i> Changer le mot de passe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
