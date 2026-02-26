<?php
declare(strict_types=1);

namespace App\Views\Participer;

require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\Participer\UpdateParticiper;
use App\Controllers\Participer\GetParticiperByIds;
use App\Controllers\Participer\ReadParticiperByIdMatch;
use App\Controllers\Joueur\GetJoueurById;
use App\Enums\Poste;

requireLogin();

if (!isset($_GET['id_match'], $_GET['id_joueur'])) {
    header('Location: /app/views/Match/ReadMatchView.php');
    exit();
}

$id_match = (int) $_GET['id_match'];
$id_joueur = (int) $_GET['id_joueur'];

/* Récupérer la participation actuelle du joueur */
$currentParticipation = (new GetParticiperByIds($id_joueur, $id_match))->execute();

if (!$currentParticipation) {
    header('Location: /app/views/Participer/ReadParticiperByIdMatchView.php?id_match=' . $id_match);
    exit();
}

/* Récupérer les postes déjà pris par les autres titulaires */
$participants = (new ReadParticiperByIdMatch($id_match))->execute();
$postesOccupes = [];
foreach ($participants as $p) {
    if ($p->getEst_Titulaire() && $p->getPoste() !== Poste::REMPLACANT->value && $p->getId_Joueur() !== $id_joueur) {
        $postesOccupes[] = $p->getPoste();
    }
}

/* Récupérer les informations du joueur */
$joueur = (new GetJoueurById($id_joueur))->execute();

/* Traitement POST pour la mise à jour */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $evaluation = $_POST['evaluation'] !== '' ? (int)$_POST['evaluation'] : null;

    $estTitulaire = (int)$_POST['est_titulaire'];
    if ($_POST['poste'] === Poste::REMPLACANT->value) {
        $estTitulaire = 0; // toujours remplaçant
    }

    $controller = new UpdateParticiper(
        $id_joueur,
        $id_match,
        $_POST['poste'],
        $estTitulaire,
        $evaluation
    );
    $controller->execute();

    //header('Location: /app/views/Participer/ReadParticiperByIdMatchView.php?id_match=' . $id_match);
    //exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la participation du joueur</title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>
<body>
<?php require_once __DIR__ . '/../../../header.php'; ?>
<div class="container">
    <div class="card">

        <h1 class="title-main">Modifier la participation du joueur</h1>
        <div class="title-divider"></div>

        <form method="post" class="form">
            <input type="hidden" name="id_match" value="<?= $id_match ?>">

            <!-- JOUEUR -->
            <div class="form-group">
                <h2 class="title-main"><?= htmlspecialchars($joueur->getNomComplet())?></h1>
                
            </div>

            <!-- POSTE -->
            <div class="form-group">
                <label class="form-label">Poste</label>
                <select name="poste" class="form-select" required>
                    <?php foreach (Poste::cases() as $poste): ?>
                        <?php
                        // Masquer les postes déjà pris sauf REMPLACANT ou poste actuel du joueur
                        if (
                            $poste !== Poste::REMPLACANT &&
                            $poste->value !== $currentParticipation->getPoste() &&
                            in_array($poste->value, $postesOccupes, true)
                        ) continue;
                        ?>
                        <option value="<?= $poste->value ?>" <?= $poste->value === $currentParticipation->getPoste() ? 'selected' : '' ?>>
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
                    <option value="1" <?= $currentParticipation->getEst_Titulaire() ? 'selected' : '' ?>>Titulaire</option>
                    <option value="0" <?= !$currentParticipation->getEst_Titulaire() ? 'selected' : '' ?>>Remplaçant</option>
                </select>
            </div>

            <!-- ÉVALUATION -->
            <div class="form-group">
                <label class="form-label">Évaluation</label>
                <input type="number" name="evaluation" class="form-select"
                       value="<?= htmlspecialchars($currentParticipation->getEvaluation() ?? '') ?>"
                       min="0" max="10">
            </div>

            <!-- ACTIONS -->
            <div class="button-group margin-top-large">
                <button type="submit" class="button button-primary">Mettre à jour</button>
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
