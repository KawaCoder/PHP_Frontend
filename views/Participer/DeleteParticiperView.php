<?php
namespace App\Views\Participer;

require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\Participer\DeleteParticiper;
use App\Controllers\Joueur\GetJoueurById;
use App\Enums\Poste;

requireLogin();

if (!isset($_GET['id_match'], $_GET['id_joueur'])) {
    header('Location: /app/views/Match/ReadMatchView.php');
    exit();
}

$id_match  = (int) $_GET['id_match'];
$id_joueur = (int) $_GET['id_joueur'];

/* Récupération du joueur */
$joueur = (new GetJoueurById($id_joueur))->execute();

if (!$joueur) {
    die('Joueur introuvable.');
}

/* Traitement POST pour la suppression */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new DeleteParticiper($id_joueur, $id_match);
    $controller->execute();

    /* Redirection vers la page de sélection */
    header("Location: ReadParticiperByIdMatchView.php?id_match=$id_match");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer un participant</title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>
<body>
<?php require_once __DIR__ . '/../../../header.php'; ?>
<div class="container">
    <div class="card">

        <h1 class="title-main">Confirmation de suppression</h1>
        <div class="title-divider"></div>

        <p>Voulez-vous vraiment supprimer le participant suivant du match ?</p>

        <div class="player-card selected">
            <div class="player-card-content">
                <h3 class="player-card-name"><?= htmlspecialchars($joueur->getNomComplet()) ?></h3>
                <div class="player-card-info">
                    <span><strong>Taille :</strong> <?= $joueur->getTaille() ?> cm</span>
                    <span><strong>Poids :</strong> <?= $joueur->getPoids() ?> kg</span>
                    <span><strong>Statut :</strong> <?= htmlspecialchars($joueur->getStatutJoueur()) ?></span>
                </div>
            </div>
        </div>

        <form method="post" class="form margin-top-large">
            <div class="button-group">
                <button type="submit" class="button button-primary">
                    Confirmer la suppression
                </button>
                <a href="/app/views/Participer/ReadParticiperByIdMatchView.php?id_match=<?= $id_match ?>">
                    <button class="button button-secondary">
                        Annuler
                    </button>
                </a>
            </div>
        </form>

    </div>
</div>

</body>
</html>
