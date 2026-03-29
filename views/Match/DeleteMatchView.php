<?php
namespace App\Views\Match;

require_once __DIR__ . '/../../config/bootstrap.php';
requireLogin();

use App\Controllers\Match\DeleteMatch;

if (isset($_GET['id_match'])) {
    $id = intval($_GET['id_match']);
    $controller = new DeleteMatch($id);
} else {
    header("Location: ReadMatchView.php");
}

if (isset($_POST['action']) && $_POST['action'] === 'supprimer') {
    $controller->execute();
    header("Location: ReadMatchView.php");
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Confirmation suppression</title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>

<body class="delete-page-wrapper">
    <div class="delete-card">
        <h2 class="delete-card__title">Supprimer le match</h2>

        <p class="delete-card__text">
            Êtes-vous sûr de vouloir supprimer ce match ? Cette action est <strong>irréversible</strong>.
        </p>

        <form method="POST" class="delete-card__actions">
            <button type="submit" name="action" value="supprimer"
                class="button button-primary">
                Oui, supprimer
            </button>

            <a href="/views/match/ReadMatchView.php" class="button button-secondary">
                Annuler
            </a>
        </form>
    </div>
</body>

</html>