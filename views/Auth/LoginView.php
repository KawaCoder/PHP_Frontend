<?php
namespace App\Views\Auth;

require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\Auth\LoginController;

$error = null;

if (isLoggedIn()) {
    header('Location: /index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $controller = new LoginController($username, $password);
    if ($controller->execute()) {
        header('Location: /index.php');
        exit();
    } else {
        $error = "Identifiants invalides.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion - Rugby Manager</title>
    <link rel="stylesheet" href="/public/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .login-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f1f5f9;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="card login-card">
            <h1 class="title-main">Rugby Manager</h1>
            <p style="text-align: center; color: #64748b; margin-bottom: 2rem;">Accès Administrateur</p>

            <?php if ($error): ?>
                <div
                    style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form">
                <div class="form-group">
                    <label class="form-label">Identifiant</label>
                    <input type="text" name="username" class="form-input" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-input" required>
                </div>
                <button type="submit" class="button button-primary"
                    style="width: 100%; margin-top: 1rem; padding: 0.75rem;">
                    Se connecter
                </button>
            </form>

            <p style="margin-top: 1.5rem; font-size: 0.8rem; color: #94a3b8; text-align: center;">
                Utilisez admin / admin pour la démo.
            </p>
        </div>
    </div>
</body>

</html>