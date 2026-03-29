<?php
namespace App\Views\Match;

require_once __DIR__ . '/../../config/bootstrap.php';
requireLogin();

use App\Controllers\Match\CreateMatch;
use Exception;

$previousPage = $_SERVER['HTTP_REFERER'] ?? '/';
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
        $points_marques = isset($_POST['points_marques']) ? (int) $_POST['points_marques'] : null;
        $points_subis = isset($_POST['points_subis']) ? (int) $_POST['points_subis'] : null;
        $sens_match = $_POST['sens_match'] ?? '';
        $sens_match = in_array($sens_match, ['GAGNE', 'PERDU', 'NUL'], true) ? $sens_match : null;

        if (
            !$date_match ||
            $nom_equipe_adverse === ''
        ) {
            throw new Exception('Données invalides');
        }

        $controller = new CreateMatch(
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

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Créer un match</title>
    <link rel="stylesheet" href="/public/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>
<?php require_once ROOT . '/header.php'; ?>
    <nav style="background: #1e293b; color: white; padding: 1rem 2rem; margin-bottom: 2rem;">
        <div
            style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: bold; font-size: 1.2rem;">Rugby Manager</span>
            <div>
                <a href="/" style="color: white; text-decoration: none; margin-right: 15px;">Accueil</a>
                <a href="../Joueur/JoueurView.php"
                    style="color: white; text-decoration: none; margin-right: 15px;">Joueurs</a>
                <a href="#" style="color: white; text-decoration: underline;">Matchs</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card">

            <h1 class="title-main">Créer un match</h1>
            <div class="title-divider"></div>

            <?php if ($error): ?>
                <p class="center"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="center">Match créé avec succès</p>
            <?php endif; ?>

            <form method="POST" class="form">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date du match</label>
                        <input type="date" name="date_match" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Heure du match</label>
                        <input type="time" name="heure_match" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Équipe adverse</label>
                    <input type="text" name="nom_equipe_adverse" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Domiciliation</label>
                    <select name="domiciliation" class="form-select">
                        <option value="">-- Sélectionner --</option>
                        <option value="DOMICILE">Domicile</option>
                        <option value="EXTERIEUR">Extérieur</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Lieu de la rencontre</label>
                    <input type="text" name="lieu_de_rencontre" class="form-input" placeholder="Stade, adresse, ville">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Points marqués</label>
                        <input type="number" name="points_marques" class="form-input" min="0" id="points_marques">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Points subis</label>
                        <input type="number" name="points_subis" class="form-input" min="0" id="points_subis">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Résultat</label>
                    <select name="sens_match_display" class="form-select" id="sens_match_display" disabled>
                        <option value="">-- Sélectionner --</option>
                        <option value="GAGNE">Victoire</option>
                        <option value="PERDU">Défaite</option>
                        <option value="NUL">Match nul</option>
                    </select>
                    <input type="hidden" name="sens_match" id="sens_match">
                </div>

                <div class="center margin-top-large">
                    <div class="button-group">
                        <a href="<?= htmlspecialchars($previousPage) ?>" class="button button-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="button button-primary">
                            Enregistrer le match
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        function updateResult() {
            const pointsMarques = parseInt(document.getElementById('points_marques').value) || 0;
            const pointsSubis = parseInt(document.getElementById('points_subis').value) || 0;
            const sensMatchDisplay = document.getElementById('sens_match_display');
            const sensMatch = document.getElementById('sens_match');

            let resultValue = '';

            if (pointsMarques > pointsSubis) {
                resultValue = 'GAGNE';
            } else if (pointsMarques < pointsSubis) {
                resultValue = 'PERDU';
            } else if (pointsMarques === pointsSubis && pointsMarques > 0) {
                resultValue = 'NUL';
            }

            sensMatchDisplay.value = resultValue;
            sensMatch.value = resultValue;
        }

        document.getElementById('points_marques').addEventListener('input', updateResult);
        document.getElementById('points_subis').addEventListener('input', updateResult);
    </script>
</body>

</html>