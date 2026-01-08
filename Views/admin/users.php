<?php
session_start();
require_once __DIR__ . '/../../services/UserService.php';
require_once __DIR__ . '/../../Core/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$db = Database::connect();
$userService = new UserService();
$message = '';
$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['activate'])) {
            $userService->activateUser($_POST['user_id']);
            $message = "Utilisateur activé avec succès";
        } elseif (isset($_POST['deactivate'])) {
            $userService->deactivateUser($_POST['user_id']);
            $message = "Utilisateur désactivé avec succès";
        } elseif (isset($_POST['update_role'])) {
            $userService->updateRole($_POST['role'], $_POST['user_id']);
            $message = "Rôle mis à jour avec succès";
        } elseif (isset($_POST['delete'])) {
            $userService->deleteUser($_POST['user_id']);
            $message = "Utilisateur supprimé avec succès";
        }
    }
    
    $users = $userService->getAllUsers();
    $stats = [
        'total' => count($users),
        'active' => array_filter($users, fn($u) => $u->getStatut() === 'actif'),
        'admins' => array_filter($users, fn($u) => $u->getRole() === 'admin'),
        'chefs' => array_filter($users, fn($u) => $u->getRole() === 'chef_projet'),
        'membres' => array_filter($users, fn($u) => $u->getRole() === 'membre')
    ];
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        .header { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .users-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #4a6fa5;
            color: white;
            padding: 15px;
            text-align: left;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover { background: #f9f9f9; }
        
        .status-active { color: green; font-weight: bold; }
        .status-inactive { color: #999; }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            color: white;
        }
        
        .badge-admin { background: #dc3545; }
        .badge-chef { background: #ffc107; color: #000; }
        .badge-membre { background: #28a745; }
        
        .actions { display: flex; gap: 5px; }
        
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
        }
        
        .btn-edit { background: #17a2b8; color: white; }
        .btn-activate { background: #28a745; color: white; }
        .btn-deactivate { background: #ffc107; color: black; }
        .btn-delete { background: #dc3545; color: white; }
        
        .search-box {
            margin: 20px 0;
            padding: 10px;
            background: white;
            border-radius: 5px;
        }
        
        .search-box input {
            width: 300px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👥 Gestion des Utilisateurs</h1>
            <p>Administration des comptes utilisateurs</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Utilisateurs</h3>
                <div class="stat-number"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Actifs</h3>
                <div class="stat-number"><?php echo count($stats['active']); ?></div>
            </div>
            <div class="stat-card">
                <h3>Administrateurs</h3>
                <div class="stat-number"><?php echo count($stats['admins']); ?></div>
            </div>
            <div class="stat-card">
                <h3>Chefs Projet</h3>
                <div class="stat-number"><?php echo count($stats['chefs']); ?></div>
            </div>
            <div class="stat-card">
                <h3>Membres</h3>
                <div class="stat-number"><?php echo count($stats['membres']); ?></div>
            </div>
        </div>
        
        <div class="search-box">
            <form method="GET">
                <input type="text" name="search" placeholder="Rechercher utilisateur..." 
                       value="<?php echo $_GET['search'] ?? ''; ?>">
                <button type="submit" class="btn">🔍 Rechercher</button>
            </form>
        </div>
        
        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user->getId(); ?></td>
                        <td><?php echo htmlspecialchars($user->getNom()); ?></td>
                        <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                <select name="role" onchange="this.form.submit()">
                                    <option value="admin" <?php echo $user->getRole() === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="chef_projet" <?php echo $user->getRole() === 'chef_projet' ? 'selected' : ''; ?>>Chef Projet</option>
                                    <option value="membre" <?php echo $user->getRole() === 'membre' ? 'selected' : ''; ?>>Membre</option>
                                </select>
                                <input type="hidden" name="update_role" value="1">
                            </form>
                        </td>
                        <td class="<?php echo $user->getStatut() === 'actif' ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo $user->getStatut(); ?>
                        </td>
                        <td><?php echo $user->getUserId($id); ?></td>
                        <td class="actions">
                            <?php if ($user->getStatut() === 'actif'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                    <button type="submit" name="deactivate" class="btn btn-deactivate" 
                                            onclick="return confirm('Désactiver cet utilisateur?')">Désactiver</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                    <button type="submit" name="activate" class="btn btn-activate">Activer</button>
                                </form>
                            <?php endif; ?>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                <button type="submit" name="delete" class="btn btn-delete"
                                        onclick="return confirm('Supprimer définitivement cet utilisateur?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>