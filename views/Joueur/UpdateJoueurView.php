<?php
namespace App\Views\Joueur;

require_once __DIR__ . '/../../config/bootstrap.php';
requireLogin();

use App\Controllers\Joueur\GetJoueurById;
use App\Controllers\Joueur\UpdateJoueur;
use App\Controllers\Joueur\DeleteJoueur;
use Exception;

$error = null;
$success = null;

// Récupération du joueur à modifier
$id_joueur = $_GET['id'] ?? null;
if (!$id_joueur) {
    header('Location: JoueurView.php');
    exit;
}

$getController = new GetJoueurById($id_joueur);
$joueur = $getController->execute();

if (!$joueur) {
    header('Location: JoueurView.php');
    exit;
}

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action']) && $_POST['action'] === 'update_joueur') {
            $nom = trim($_POST['nom_joueur'] ?? '');
            $prenom = trim($_POST['prenom_joueur'] ?? '');
            $licence = trim($_POST['numero_licence'] ?? '');
            $date_naiss = !empty($_POST['date_naiss']) ? $_POST['date_naiss'] : null;

            $taille_raw = $_POST['taille'] ?? '';
            $taille = ($taille_raw === '') ? null : filter_var($taille_raw, FILTER_VALIDATE_FLOAT);

            $poids_raw = $_POST['poids'] ?? '';
            $poids = ($poids_raw === '') ? null : filter_var($poids_raw, FILTER_VALIDATE_FLOAT);

            $statut = $_POST['statut_joueur'] ?? 'ACTIF';
            $commentaire_general = trim($_POST['commentaire_general'] ?? '');

            $updateController = new UpdateJoueur(
                $id_joueur,
                $nom,
                $prenom,
                $licence,
                $date_naiss,
                $taille,
                $poids,
                $statut,
                $commentaire_general
            );
            $updateController->execute();

            $joueur = $getController->execute();
            $success = "Informations du joueur mises à jour !";

        } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_joueur') {
            $deleteController = new DeleteJoueur($id_joueur);
            $deleteController->execute();

            header('Location: JoueurView.php?success=Joueur supprimé');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Joueur - <?= htmlspecialchars($joueur['prenom_joueur'] . ' ' . $joueur['nom_joueur']) ?></title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>

<body>
    <?php require_once ROOT . '/header.php'; ?>
    <div class="container">

        <div style="margin-bottom: 2rem;">
            <a href="JoueurView.php"><button class="button button-secondary">← Retour à la liste</button></a>
        </div>

        <div class="card">
            <h2 class="title-main">Dossier :
                <?= htmlspecialchars($joueur['prenom_joueur'] . ' ' . $joueur['nom_joueur']) ?></h2>

            <?php if ($success): ?>
                <div class="team-status team-status-ok"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="team-status team-status-warning"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Formulaire de modification -->
            <form method="POST">
                <input type="hidden" name="action" value="update_joueur">

                <div class="form-row">
                    <div class="form-group" style="flex:1">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom_joueur" class="form-input"
                            value="<?= htmlspecialchars($joueur['nom_joueur']) ?>" required>
                    </div>
                    <div class="form-group" style="flex:1">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom_joueur" class="form-input"
                            value="<?= htmlspecialchars($joueur['prenom_joueur']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">N° Licence</label>
                    <input type="text" name="numero_licence" class="form-input"
                        value="<?= htmlspecialchars($joueur['numero_licence']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Date de Naissance</label>
                    <input type="date" name="date_naiss" class="form-input"
                        value="<?= htmlspecialchars($joueur['date_naiss'] ?? '') ?>">
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex:1">
                        <label class="form-label">Taille (cm)</label>
                        <input type="number" name="taille" class="form-input" step="0.01"
                            value="<?= htmlspecialchars($joueur['taille'] ?? '') ?>">
                    </div>
                    <div class="form-group" style="flex:1">
                        <label class="form-label">Poids (kg)</label>
                        <input type="number" name="poids" class="form-input" step="0.01"
                            value="<?= htmlspecialchars($joueur['poids'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select name="statut_joueur" class="form-select">
                        <?php
                        $statuts = ['ACTIF', 'BLESSE', 'SUSPENDU', 'ABSENT'];
                        $current = strtoupper($joueur['statut_joueur']);
                        foreach ($statuts as $s): ?>
                            <option value="<?= $s ?>" <?= $current === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Commentaire</label>
                    <textarea name="commentaire_general" class="form-textarea"
                        rows="4"><?= htmlspecialchars($joueur['commentaire'] ?? '') ?></textarea>
                </div><br>

                <button type="submit" class="button button-primary" style="width: 100%;">Mettre à jour les
                    infos</button>
            </form>

            <!-- Formulaire de suppression -->
            <form method="POST" style="margin-top: 1rem;">
                <input type="hidden" name="action" value="delete_joueur">
                <button type="submit" class="button button-secondary"
                    style="width: 100%; border-color: #ef4444; color: #ef4444;">
                    Supprimer le joueur
                </button>
            </form>

        </div>

    </div>
</body>

</html>