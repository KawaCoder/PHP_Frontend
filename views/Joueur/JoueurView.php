<?php
namespace App\Views\Joueur;

require_once __DIR__ . '/../../config/bootstrap.php';
requireLogin();

use App\Controllers\Joueur\CreateJoueur;
use App\Controllers\Joueur\ReadJoueur;
use Exception;

$error = null;
$success = $_GET['success'] ?? null;

// Traitement du formulaire de création
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    try {
        $nom_joueur = trim($_POST['nom_joueur'] ?? '');
        $prenom_joueur = trim($_POST['prenom_joueur'] ?? '');
        $numero_licence = trim($_POST['numero_licence'] ?? '');
        $date_naiss = !empty($_POST['date_naiss']) ? $_POST['date_naiss'] : null;

        $taille_raw = $_POST['taille'] ?? '';
        $taille = ($taille_raw === '') ? null : filter_var($taille_raw, FILTER_VALIDATE_FLOAT);
        if ($taille_raw !== '' && $taille === false)
            throw new Exception('La taille doit être un nombre.');

        $poids_raw = $_POST['poids'] ?? '';
        $poids = ($poids_raw === '') ? null : filter_var($poids_raw, FILTER_VALIDATE_FLOAT);
        if ($poids_raw !== '' && $poids === false)
            throw new Exception('Le poids doit être un nombre.');

        $statut_joueur = $_POST['statut_joueur'] ?? '';
        $commentaire = trim($_POST['commentaire'] ?? '');

        $controller = new CreateJoueur(
            $nom_joueur,
            $prenom_joueur,
            $numero_licence,
            $date_naiss,
            $taille,
            $poids,
            $statut_joueur,
            $commentaire
        );
        $controller->execute();
        $success = "Joueur ajouté avec succès !";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Récupération des joueurs
try {
    $readController = new ReadJoueur();
    $joueurs = $readController->execute();
} catch (Exception $e) {
    $joueurs = [];
    $error = "Impossible de récupérer les joueurs. " . $e->getMessage();
}

$showForm = isset($_GET['show_form']) && $_GET['show_form'] === '1';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des Joueurs</title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>

<body>
    <?php require_once __DIR__ . '/../../../header.php'; ?>
    <div class="container-wide">
        <br>
        <div class="button-group margin-bottom-medium" style="justify-content: flex-end;">
            <?php if ($showForm): ?>
                <a href="?"><button class="button button-primary">Fermer le formulaire</button></a>
            <?php else: ?>
                <a href="?show_form=1"><button class="button button-primary">Ajouter un joueur</button></a>
            <?php endif; ?>
        </div>

        <?php if ($success): ?>
            <div class="team-status team-status-ok"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="page-container">

            <!-- Liste des joueurs -->
            <div class="section-list">
                <h1 class="title-main">Effectif du club</h1>

                <?php if (empty($joueurs)): ?>
                    <div class="card center">
                        <p><?= $error ? 'Erreur : ' . htmlspecialchars($error) : 'Aucun joueur dans l\'effectif pour le moment.' ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="player-grid">
                        <?php foreach ($joueurs as $joueur): ?>
                            <div class="player-card">
                                <div class="player-card-content">
                                    <h3 class="player-card-name">
                                        <?= htmlspecialchars($joueur['prenom_joueur']) ?>
                                        <span
                                            style="text-transform: uppercase;"><?= htmlspecialchars($joueur['nom_joueur']) ?></span>
                                    </h3>
                                    <div class="player-card-info">
                                        <span>Licence: <?= htmlspecialchars($joueur['numero_licence']) ?></span>
                                        <span><?= htmlspecialchars($joueur['taille'] ?? '-') ?> cm /
                                            <?= htmlspecialchars($joueur['poids'] ?? '-') ?> kg</span>
                                        <span><?= $joueur['date_naiss'] ? date('d/m/Y', strtotime($joueur['date_naiss'])) : 'Non renseigné' ?></span>
                                    </div>
                                </div>
                                <div class="player-card-footer">
                                    <?php
                                    $statut = strtoupper($joueur['statut_joueur']);
                                    $badgeClass = match ($statut) {
                                        'ACTIF' => 'badge-actif',
                                        'BLESSE' => 'badge-blesse',
                                        'SUSPENDU' => 'badge-suspendu',
                                        'ABSENT' => 'badge-blesse', // on peut utiliser même style que blessé
                                        default => ''
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($statut) ?></span>
                                    <a href="UpdateJoueurView.php?id=<?= $joueur['id_joueur'] ?>">
                                        <button class="button button-secondary">
                                            Modifier
                                        </button>
                                    </a>
                                    <a
                                        href="/app/views/participer/ReadParticiperByIdJoueurView.php?id_joueur=<?= $joueur['id_joueur'] ?>">
                                        <button class="button button-secondary">
                                            Matchs
                                        </button>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Formulaire ajout joueur -->
            <?php if ($showForm): ?>
                <div class="section-form">
                    <h2>Nouveau Joueur</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="create">

                        <div class="form-group">
                            <label>Nom <span style="color:red">*</span></label>
                            <input type="text" name="nom_joueur" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label>Prénom <span style="color:red">*</span></label>
                            <input type="text" name="prenom_joueur" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label>N° Licence <span style="color:red">*</span></label>
                            <input type="text" name="numero_licence" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label>Date Naissance</label>
                            <input type="date" name="date_naiss" class="form-input">
                        </div>

                        <div class="form-row">
                            <div class="form-group" style="flex:1">
                                <label>Taille (cm)</label>
                                <input type="number" name="taille" class="form-input" step="0.01">
                            </div>
                            <div class="form-group" style="flex:1">
                                <label>Poids (kg)</label>
                                <input type="number" name="poids" class="form-input" step="0.01">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Statut</label>
                            <select name="statut_joueur" class="form-select">
                                <option value="ACTIF">Actif</option>
                                <option value="BLESSE">Blessé</option>
                                <option value="SUSPENDU">Suspendu</option>
                                <option value="ABSENT">Absent</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Commentaire</label>
                            <textarea name="commentaire" class="form-textarea" rows="2"></textarea>
                        </div>

                        <button type="submit" class="button button-primary" style="width:100%; margin-top:1rem;">
                            Ajouter à l'effectif
                        </button>
                    </form>
                </div>
            <?php endif; ?>

        </div>

    </div>
</body>

</html>