<?php
namespace App\Views\Match;

require_once __DIR__ . '/../../config/bootstrap.php';
requireLogin();

use App\Controllers\Match\UpdateMatch;
use App\Controllers\Match\GetMatchById;
use Exception;

$previousPage = $_SERVER['HTTP_REFERER'] ?? '/';

if (!isset($_GET['id_match'])) {
    header("Location: ReadMatchView.php");
    exit();
}

$id_match = intval($_GET['id_match']);
$match = new GetMatchById($id_match);
$match = $match->execute();

$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $date = $_POST['date_match'] ?? null;
        $time = $_POST['heure_match'] ?? null;
        $date_match = $date && $time ? $date . ' ' . $time . ':00' : null;

        $nom_equipe_adverse = trim($_POST['nom_equipe_adverse'] ?? '');
        $domiciliation = $_POST['domiciliation'] ?? '';
        $domiciliation = in_array($domiciliation, ['DOMICILE', 'EXTERIEUR'], true) ? $domiciliation : null;

        $lieu_de_rencontre = trim($_POST['lieu_de_rencontre'] ?? '');
        $lieu_de_rencontre = $lieu_de_rencontre !== '' ? $lieu_de_rencontre : null;

        $points_marques = isset($_POST['points_marques']) ? (int) $_POST['points_marques'] : 0;
        $points_subis = isset($_POST['points_subis']) ? (int) $_POST['points_subis'] : 0;

        // Calcul du résultat côté PHP
        if ($points_marques > $points_subis) {
            $sens_match = 'GAGNE';
        } elseif ($points_marques < $points_subis) {
            $sens_match = 'PERDU';
        } elseif ($points_marques === $points_subis && $points_marques > 0) {
            $sens_match = 'NUL';
        } else {
            $sens_match = null; // pas de points, match non terminé
        }

        if (!$date_match || $nom_equipe_adverse === '') {
            throw new Exception('Données invalides');
        }

        $controller = new UpdateMatch(
            $id_match,
            $date_match,
            $nom_equipe_adverse,
            $lieu_de_rencontre,
            $points_subis,
            $points_marques,
            $domiciliation,
            $sens_match
        );

        $controller->execute();
        $success = true;
        header("Location: ReadMatchView.php");
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier un match</title>
    <link rel="stylesheet" href="/public/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>
<?php require_once __DIR__ . '/../../../header.php'; ?>
    <div class="container">

        <div class="card">

            <h1 class="title-main">Modifier un match</h1>
            <div class="title-divider"></div>

            <?php if ($error): ?>
                <p class="center" style="color:red"><?= htmlspecialchars($error) ?></p>
            <?php elseif ($success): ?>
                <p class="center" style="color:green">Le match a été modifié avec succès.</p>
            <?php endif; ?>

            <form method="POST" class="form">

                <div class="date-time-row">
                    <div class="form-group">
                        <label for="date_match" class="form-label">Date du match<span class="required">*</span></label>
                        <input type="date" name="date_match" id="date_match" class="form-input" required
                            value="<?= $match->getDate_match() ? date('Y-m-d', strtotime($match->getDate_match())) : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="heure_match" class="form-label">Heure du match<span
                                class="required">*</span></label>
                        <input type="time" name="heure_match" id="heure_match" class="form-input" required
                            value="<?= $match->getDate_match() ? date('H:i', strtotime($match->getDate_match())) : '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="nom_equipe_adverse" class="form-label">Équipe adverse<span
                            class="required">*</span></label>
                    <input type="text" name="nom_equipe_adverse" id="nom_equipe_adverse" class="form-input" required
                        value="<?= htmlspecialchars($match->getNom_equipe_adverse()) ?>">
                </div>

                <div class="form-group">
                    <label for="domiciliation" class="form-label">Domiciliation</label>
                    <select name="domiciliation" id="domiciliation" class="form-select">
                        <option value="">-- Sélectionner --</option>
                        <option value="DOMICILE" <?= $match->getDomiciliation() === 'DOMICILE' ? 'selected' : '' ?>>
                            Domicile</option>
                        <option value="EXTERIEUR" <?= $match->getDomiciliation() === 'EXTERIEUR' ? 'selected' : '' ?>>
                            Extérieur</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="lieu_de_rencontre" class="form-label">Lieu du match</label>
                    <input type="text" name="lieu_de_rencontre" id="lieu_de_rencontre" class="form-input"
                        placeholder="Stade, adresse, ville"
                        value="<?= htmlspecialchars($match->getLieu_de_rencontre() ?? '') ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="points_marques" class="form-label">Points marqués</label>
                        <input type="number" name="points_marques" id="points_marques" class="form-input"
                            value="<?= $match->getPoints_marques() !== null ? intval($match->getPoints_marques()) : '' ?>"
                            min="0">
                    </div>

                    <div class="form-group">
                        <label for="points_subis" class="form-label">Points subis</label>
                        <input type="number" name="points_subis" id="points_subis" class="form-input"
                            value="<?= $match->getPoints_subis() !== null ? intval($match->getPoints_subis()) : '' ?>"
                            min="0">
                    </div>
                </div>

                <div class="form-group">
                    <label for="sens_match" class="form-label">Résultat</label>
                    <select name="sens_match_display" id="sens_match_display" class="form-select" disabled>
                        <option value="">-- Sélectionner --</option>
                        <option value="GAGNE" <?= $match->getSens_match() === 'GAGNE' ? 'selected' : '' ?>>Victoire</option>
                        <option value="PERDU" <?= $match->getSens_match() === 'PERDU' ? 'selected' : '' ?>>Défaite</option>
                        <option value="NUL" <?= $match->getSens_match() === 'NUL' ? 'selected' : '' ?>>Match nul</option>
                    </select>
                    <input type="hidden" name="sens_match" id="sens_match"
                        value="<?= htmlspecialchars($match->getSens_match() ?? '') ?>">

                </div>

                <div class="button-group">
                    <button type="submit" class="button button-primary">
                        Modifier le Match
                    </button>
                    <a href="<?= htmlspecialchars($previousPage) ?>">
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