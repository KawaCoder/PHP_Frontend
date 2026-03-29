<?php
declare(strict_types=1);

namespace App\Views\Participer;

require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\Participer\ReadParticiperByIdMatch;
use App\Controllers\Joueur\ReadJoueur;
use App\Controllers\Participer\CreateParticiper;
use App\Enums\Poste;

requireLogin();

if (!isset($_GET['id_match'])) {
    header('Location: /views/Match/ReadMatchView.php');
    exit();
}

$id_match = (int) $_GET['id_match'];

/* Participants déjà inscrits */
$participants = (new ReadParticiperByIdMatch($id_match))->execute();

$joueursInscrits = [];
$postesOccupes  = [];

foreach ($participants as $p) {
    $joueursInscrits[] = $p->getId_Joueur();

    if ($p->getEst_Titulaire() && $p->getPoste() !== Poste::REMPLACANT->value) {
        $postesOccupes[] = $p->getPoste();
    }
}

/* Joueurs actifs */
$joueurs       = (new ReadJoueur())->execute();
$joueursActifs = [];

foreach ($joueurs as $joueur) {
    if ($joueur->getStatutJoueur() === 'ACTIF') {
        $joueursActifs[] = $joueur;
    }
}

/* Joueurs disponibles */
$joueursDisponibles = array_filter(
    $joueursActifs,
    fn ($j) => !in_array($j->getIdJoueur(), $joueursInscrits, true)
);

/* Message de confirmation */
$confirmationMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CreateParticiper(
        $_POST['id_joueur'],
        $id_match,
        $_POST['poste'],
        $_POST['est_titulaire'],
        NULL
    );
    $controller->execute();

    header("Location: CreateParticiperView.php?id_match=". $id_match);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un joueur au match</title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>
<body>
<?php require_once __DIR__ . '/../../../header.php'; ?>
<div class="container">
    <div class="card">

        <h1 class="title-main">Ajouter un joueur au match</h1>
        <div class="title-divider"></div>

        <!-- MESSAGE DE CONFIRMATION -->
        <form method="post" class="form">
            <input type="hidden" name="id_match" value="<?= $id_match ?>">

            <!-- JOUEUR -->
            <div class="form-group">
                <label class="form-label">Joueur</label>
                <select name="id_joueur" class="form-select" required>
                    <option value="">— Sélectionner un joueur —</option>
                    <?php foreach ($joueursDisponibles as $joueur): ?>
                        <option value="<?= $joueur->getIdJoueur() ?>">
                            <?= htmlspecialchars($joueur->getNomComplet()) ?>
                            (<?= $joueur->getTaille() ?> cm / <?= $joueur->getPoids() ?> kg)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- POSTE -->
            <div class="form-group">
                <label class="form-label">Poste</label>
                <select name="poste" class="form-select" required>
                    <option value="">— Sélectionner un poste —</option>
                    <?php foreach (Poste::cases() as $poste): ?>
                        <?php if ($poste !== Poste::REMPLACANT && in_array($poste->value, $postesOccupes, true)) continue; ?>
                        <option value="<?= $poste->value ?>">
                            <?= htmlspecialchars($poste->label()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-hint">Un seul titulaire par poste (sauf remplaçant)</small>
            </div> 

            <!-- STATUT -->
            <div class="form-group">
                <label class="form-label">Statut</label>
                <select name="est_titulaire" class="form-select" required>
                    <option value="1">Titulaire</option>
                    <option value="0">Remplaçant</option>
                </select>
            </div>

            <!-- ACTIONS -->
            <div class="button-group margin-top-large">
                <button type="submit" class="button button-primary">
                    Ajouter au match
                </button>

                <a href="/views/Participer/ReadParticiperByIdMatchView.php?id_match=<?= $id_match ?>" class="button button-secondary">
                    Retour
                </a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
