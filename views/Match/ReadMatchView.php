<?php
namespace App\Views\Match;

require_once __DIR__ . '/../../config/bootstrap.php';
requireLogin();

use App\Controllers\Match\ReadMatch;
use App\Controllers\Participer\ReadParticiperByIdMatch;
use App\Controllers\Joueur\GetJoueurById;
use Exception;

$error = null;

try {
    $matchs = (new ReadMatch())->execute();
} catch (Exception $e) {
    $error = $e->getMessage();
}

$now = time();
$matchsAVenir = [];
$matchsTermines = [];

foreach ($matchs as $match) {
    if (strtotime($match->getDate_match()) > $now) {
        $matchsAVenir[] = $match;
    } else {
        $matchsTermines[] = $match;
    }
}

/**
 * Compte les titulaires ACTIFS d’un match
 */
function countTitulairesActifs(int $idMatch): int
{
    $participants = (new ReadParticiperByIdMatch($idMatch))->execute();
    $count = 0;

    foreach ($participants as $p) {
        if ($p->getEst_Titulaire()) {
            $joueur = (new GetJoueurById($p->getId_Joueur()))->execute();
            if ($joueur && $joueur->getStatutJoueur() === 'ACTIF') {
                $count++;
            }
        }
    }
    return $count;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des matchs</title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>

<body>
<?php require_once ROOT . '/header.php'; ?>
<div class="container container-wide">
    <div class="card card-wide">

        <h1 class="title-main">Liste des matchs</h1>
        <div class="title-divider"></div>

        <div class="center margin-top-large">
            <a href="CreateMatchView.php"><button class="button button-primary">Créer un Match</button></a>
        </div>

        <?php if ($error): ?>
            <p class="center error-text"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <!-- MATCHS À VENIR -->
        <h2 class="section-title">Matchs à venir</h2>

        <table class="match-table">
            <thead>
                <tr>
                    <th>Équipe adverse</th>
                    <th>Date & Heure</th>
                    <th>Domiciliation</th>
                    <th>Lieu</th>
                    <th>Titulaires actifs</th>
                    <th>Résultat</th>
                    <th>Score</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

            <?php if (!empty($matchsAVenir)): ?>
                <?php foreach ($matchsAVenir as $match):

                    $nbTitulairesActifs = countTitulairesActifs($match->getId_Match());
                ?>
                    <tr>
                        <td><?= $match->getNom_equipe_adverse() ?: '-' ?></td>
                        <td><?= $match->getDate_match()
                            ? date('d/m/Y H:i', strtotime($match->getDate_match()))
                            : '-' ?>
                        </td>
                        <td><?= $match->getDomiciliation() ?: '-' ?></td>
                        <td><?= $match->getLieu_de_rencontre() ?: '-' ?></td>

                        <td>
                            <?php if ($nbTitulairesActifs < 15): ?>
                                <span class="badge badge-blesse"><?= $nbTitulairesActifs ?> / 15</span>
                            <?php elseif ($nbTitulairesActifs == 15): ?>
                                <span class="badge badge-actif"><?= $nbTitulairesActifs ?> / 15</span>
                            <?php endif; ?>
                        </td>

                        <td><?= $match->getSens_match() ?: '-' ?></td>
                        <td><?= $match->getPoints_marques() ?? '-' ?> - <?= $match->getPoints_subis() ?? '-' ?></td>

                        <td class="actions">
                            <a href="/app/views/Participer/ReadParticiperByIdMatchView.php?id_match=<?= $match->getId_Match() ?>">
                                <button class="button button-secondary">Joueurs</button></a>
                            <a href="UpdateMatchView.php?id_match=<?= $match->getId_Match() ?>">
                                <button class="button button-secondary">Modifier</button></a>
                            <a href="DeleteMatchView.php?id_match=<?= $match->getId_Match() ?>">
                                <button class="button button-secondary">Supprimer</button></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="center">Aucun match à venir</td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>

        <br><br><br>
        <div class="title-divider"></div>

        <!-- MATCHS TERMINÉS -->
        <h2 class="section-title">Matchs terminés</h2>

        <table class="match-table">
            <thead>
                <tr>
                    <th>Équipe adverse</th>
                    <th>Date & Heure</th>
                    <th>Domiciliation</th>
                    <th>Lieu</th>
                    <th>Titulaires actifs</th>
                    <th>Résultat</th>
                    <th>Score</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

            <?php if (!empty($matchsTermines)): ?>
                <?php foreach ($matchsTermines as $match):

                    $nbTitulairesActifs = countTitulairesActifs($match->getId_Match());
                ?>
                    <tr>
                        <td><?= $match->getNom_equipe_adverse() ?: '-' ?></td>
                        <td><?= $match->getDate_match()
                            ? date('d/m/Y H:i', strtotime($match->getDate_match()))
                            : '-' ?>
                        </td>
                        <td><?= $match->getDomiciliation() ?: '-' ?></td>
                        <td><?= $match->getLieu_de_rencontre() ?: '-' ?></td>

                        <td>
                            <?php if ($nbTitulairesActifs < 15): ?>
                                <span class="badge badge-blesse"><?= $nbTitulairesActifs ?> / 15</span>
                            <?php elseif ($nbTitulairesActifs == 15): ?>
                                <span class="badge badge-actif"><?= $nbTitulairesActifs ?> / 15</span>
                            <?php endif; ?>
                        </td>

                        <td><?= $match->getSens_match() ?: '-' ?></td>
                        <td><?= $match->getPoints_marques() ?? '-' ?> - <?= $match->getPoints_subis() ?? '-' ?></td>

                        <td class="actions">
                            <a href="/app/views/Participer/ReadParticiperByIdMatchView.php?id_match=<?= $match->getId_Match() ?>">
                                <button class="button button-secondary">Joueurs</button></a>
                            <a href="UpdateMatchView.php?id_match=<?= $match->getId_Match() ?>">
                                <button class="button button-secondary">Modifier</button></a>
                            <a href="DeleteMatchView.php?id_match=<?= $match->getId_Match() ?>">
                                <button class="button button-secondary">Supprimer</button></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="center">Aucun match terminé</td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>

    </div>
</div>

</body>
</html>
