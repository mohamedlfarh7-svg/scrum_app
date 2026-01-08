<?php
if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard/dashboard.php');
    exit;
}
require_once '../../core/Auth.php';
require_once '../../services/UserService.php';
$auth = new Auth();

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'membre';

    $nom = trim($nom);
    $email = trim($email);
    $password = trim($password);
    $role = trim($role);

    if(empty($nom) || empty($email) || empty($password)){
        $error = "Tous les champs sont obligatoires !";
    } else {
        $result = $auth->register($nom, $email, $password, $role);
        
        if ($result) {
            header('Location: login.php');
            exit;
        } else {
            $error = "Erreur lors de l'inscription";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #eddcffff 0%, #dae2eeff 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 500px;
        }
        
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .logo p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            border-left: 4px solid #c62828;
        }
        
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            border-left: 4px solid #2e7d32;
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
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            background-color: white;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #9b59b6;
        }
        
        .form-group select {
            cursor: pointer;
        }
        
        .role-option {
            padding: 8px;
        }
        
        .submit-btn {
            background: linear-gradient(to right, #9b59b6, #8e44ad);
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            background: linear-gradient(to right, #8e44ad, #7d3c98);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .login-link p {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .login-btn {
            display: inline-block;
            background: #3498db;
            color: white;
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .login-btn:hover {
            background: #2980b9;
        }
        
        .terms {
            margin-top: 15px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .terms a {
            color: #3498db;
            text-decoration: none;
        }
        
        .terms a:hover {
            text-decoration: underline;
        }
        
        .role-info {
            margin-top: 8px;
            font-size: 12px;
            color: #7f8c8d;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
        
        .admin-info { border-left: 3px solid #e74c3c; }
        .chef-info { border-left: 3px solid #3498db; }
        .membre-info { border-left: 3px solid #2ecc71; }
        
        @media (max-width: 480px) {
            .register-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-card">
            <div class="logo">
                <h1>Inscription</h1>
                <p>Créez votre compte gratuit</p>
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
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nom">Nom complet:</label>
                    <input type="text" id="nom" name="nom" required 
                           placeholder="Entrez votre nom complet"
                           value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="exemple@email.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe:</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Minimum 8 caractères">
                </div>
                
                <div class="form-group">
                    <label for="role">Rôle:</label>
                    <select id="role" name="role" required>
                        <option value="membre" <?php echo (($_POST['role'] ?? '') == 'membre') ? 'selected' : ''; ?>>
                            Membre d'équipe
                        </option>
                        <option value="chef_projet" <?php echo (($_POST['role'] ?? '') == 'chef_projet') ? 'selected' : ''; ?>>
                            Chef de projet
                        </option>
                        <option value="admin" <?php echo (($_POST['role'] ?? '') == 'admin') ? 'selected' : ''; ?>>
                            Administrateur
                        </option>
                    </select>
                    
                    <?php
                    $selected_role = $_POST['role'] ?? 'membre';
                    $role_descriptions = [
                        'admin' => "Accès complet à toutes les fonctionnalités",
                        'chef_projet' => "Peut créer et gérer des projets",
                        'membre' => "Peut travailler sur les tâches assignées"
                    ];
                    $role_classes = [
                        'admin' => 'admin-info',
                        'chef_projet' => 'chef-info',
                        'membre' => 'membre-info'
                    ];
                    ?>
                    <div class="role-info <?php echo $role_classes[$selected_role]; ?>">
                        <strong>Rôle sélectionné :</strong> 
                        <?php echo ucfirst(str_replace('_', ' ', $selected_role)); ?> - 
                        <?php echo $role_descriptions[$selected_role]; ?>
                    </div>
                </div>
                
                <div class="terms">
                    En vous inscrivant, vous acceptez nos 
                    <a href="#">Conditions d'utilisation</a> et 
                    <a href="#">Politique de confidentialité</a>
                </div>
                
                <button type="submit" class="submit-btn">S'inscrire</button>
            </form>
            
            <div class="login-link">
                <p>Vous avez déjà un compte ?</p>
                <a href="login.php" class="login-btn">Se connecter</a>
            </div>
        </div>
    </div>
</body>
</html>