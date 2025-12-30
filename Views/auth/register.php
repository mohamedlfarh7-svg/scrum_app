<?php
require_once '../../core/Auth.php';
$auth = new Auth();

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ;
    $email = $_POST['email'];
    $password = $_POST['password'] ;

    $nom = trim($nom);
    $email = trim($email);
    $password = trim($password);

    if(empty($nom) || empty($email) || empty($password)){
        $error = "Tous les champs sont obligatoires !";
    } else {
        $result = $auth->register($nom, $email, $password);
        
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
<html lang="en">
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            max-width: 450px;
        }
        
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            transition: transform 0.3s ease;
        }
        
        .register-card:hover {
            transform: translateY(-5px);
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
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #9b59b6;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
            color: #7f8c8d;
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
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            background: linear-gradient(to right, #8e44ad, #7d3c98);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(155, 89, 182, 0.4);
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
            transition: all 0.3s;
        }
        
        .login-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
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
        
        .field-group {
            display: flex;
            gap: 15px;
        }
        
        .field-group .form-group {
            flex: 1;
        }
        
        @media (max-width: 480px) {
            .register-card {
                padding: 30px 20px;
            }
            
            .field-group {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
    <script>
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('password-strength');
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            const messages = [
                "Très faible",
                "Faible",
                "Moyen",
                "Fort",
                "Très fort"
            ];
            const colors = [
                "#e74c3c",
                "#e67e22",
                "#f1c40f",
                "#2ecc71",
                "#27ae60"
            ];
            
            strengthBar.textContent = "Force du mot de passe: " + messages[strength];
            strengthBar.style.color = colors[strength];
        }
    </script>
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
            
            <form method="POST" action="" id="registerForm">
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
                           placeholder="Minimum 8 caractères"
                           onkeyup="checkPasswordStrength(this.value)">
                    <div id="password-strength" class="password-strength"></div>
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