<?php
namespace App\Views\Participer;

require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\Participer\ReadParticiperByIdJoueur;
use App\Controllers\Match\GetMatchById;
use App\Controllers\Joueur\GetJoueurById;
use App\Enums\Poste;
use DateTime;

requireLogin();

$id_joueur = $_GET['id_joueur'] ?? null;
if (!$id_joueur) {
    header('Location: /app/views/Joueur/JoueurView.php');
    exit();
}

$joueur = (new GetJoueurById((int)$id_joueur))->execute();
// Récupération de toutes les participations du joueur
$participations = (new ReadParticiperByIdJoueur((int)$id_joueur))->execute();

$matchsFuturs = [];
$matchsPasses = [];

// Séparer les matchs passés et futurs
$now = new DateTime();
foreach ($participations as $p) {
    $matchController = new GetMatchById($p->getId_Match());
    $match = $matchController->execute();
    $dateMatch = new DateTime($match->getDate_Match());

    if ($dateMatch >= $now) {
        $matchsFuturs[] = ['match' => $match, 'participation' => $p];
    } else {
        $matchsPasses[] = ['match' => $match, 'participation' => $p];
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique et matchs à venir du joueur</title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>
<body>
<?php require_once __DIR__ . '/../../../header.php'; ?>
<div class="container container-wide">
    <div class="card card-wide">
        <h1 class="title-main">Matchs à venir de <?= htmlspecialchars($joueur->getNomComplet()) ?></h1>
        <div class="title-divider"></div>

        <div style="margin-bottom: 2rem;">
            <a href="/app/views/Joueur/JoueurView.php"><button class="button button-secondary">← Retour à la liste</button></a>
        </div>

        <!-- MATCHS FUTURS -->
        <h2 class="section-title">Matchs à venir</h2>
        <?php if (empty($matchsFuturs)): ?>
            <p>Pas de matchs prévus pour ce joueur.</p>
        <?php else: ?>
            <table class="match-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Adversaire</th>
                        <th>Lieu</th>
                        <th>Poste</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($matchsFuturs as $item):
                    $match = $item['match'];
                    $p = $item['participation'];
                    $statut = strtoupper($p->getEst_Titulaire() ? 'Titulaire' : 'Remplacant');
                ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($match->getDate_Match())) ?></td>
                        <td><?= htmlspecialchars($match->getNom_equipe_adverse()) ?></td>
                        <td><?= htmlspecialchars($match->getLieu_de_rencontre()) ?></td>
                        <td><?= htmlspecialchars(Poste::from($p->getPoste())->label()) ?></td>
                        <td>
                            <?php if (strtoupper($p->getEst_Titulaire()) == 0): ?>
                                <span class="badge badge-suspendu"><?= htmlspecialchars($statut) ?></span>
                            <?php else: ?>
                                <span class="badge badge-actif"><?= htmlspecialchars($statut) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a href="/app/views/Participer/UpdateParticiperView.php?id_match=<?= $match->getId_Match() ?>&id_joueur=<?= $p->getId_Joueur() ?>">
                                <button class="button button-secondary">
                                    Modifier
                                </button>
                            </a>
                            <a href="/app/views/Participer/DeleteParticiperView.php?id_match=<?= $match->getId_Match() ?>&id_joueur=<?= $p->getId_Joueur() ?>">
                                <button class="button button-secondary">
                                    Supprimer
                                </button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- MATCHS PASSÉS -->
        <h2 class="section-title">Matchs passés</h2>
        <?php if (empty($matchsPasses)): ?>
            <p>Aucun match passé pour ce joueur.</p>
        <?php else: ?>
            <table class="match-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Adversaire</th>
                        <th>Lieu</th>
                        <th>Poste</th>
                        <th>Évaluation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($matchsPasses as $item):
                    $match = $item['match'];
                    $p = $item['participation'];
                ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($match->getDate_Match())) ?></td>
                        <td><?= htmlspecialchars($match->getNom_equipe_adverse()) ?></td>
                        <td><?= htmlspecialchars($match->getLieu_de_rencontre()) ?></td>
                        <td><?= htmlspecialchars($p->getPoste()) ?></td>
                        <td><?= $p->getEvaluation() ?: '—' ?></td>
                        <td class="actions">
                            <a href="/app/views/Participer/UpdateParticiperView.php?id_match=<?= $match->getId_Match() ?>&id_joueur=<?= $p->getId_Joueur() ?>">
                                <button class="button button-secondary">
                                    Modifier
                                </button>
                            </a>
                            <a href="/app/views/Participer/DeleteParticiperView.php?id_match=<?= $match->getId_Match() ?>&id_joueur=<?= $p->getId_Joueur() ?>">
                                <button class="button button-secondary">
                                    Supprimer
                                </button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
