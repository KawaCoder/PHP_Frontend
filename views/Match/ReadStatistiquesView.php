<?php
namespace App\Views\Statistiques;

require_once __DIR__ . '/../../config/bootstrap.php';
requireLogin();

use App\Controllers\Match\ReadMatch;
use App\Controllers\Joueur\ReadJoueur;
use App\Controllers\Participer\ReadParticiperByIdMatch;
use App\Enums\Poste;

/* =========================
   DONNÉES GLOBALES MATCHS
========================= */

$matchs = (new ReadMatch())->execute();

$totalMatchs = count($matchs);
$gagnes = $perdus = $nuls = 0;

foreach ($matchs as $match) {
    match ($match->getSens_match()) {
        'GAGNE' => $gagnes++,
        'PERDU' => $perdus++,
        'NUL'   => $nuls++,
        default => null
    };
}

function percent(int $val, int $total): string
{
    return $total > 0 ? round(($val / $total) * 100, 1) . ' %' : '0 %';
}

/* =========================
   DONNÉES JOUEURS
========================= */

$joueurs = (new ReadJoueur())->execute();

/**
 * Récupère toutes les participations d’un joueur
 */
function getParticipationsJoueur(int $idJoueur, array $matchs): array
{
    $result = [];

    foreach ($matchs as $match) {
        $participants = (new ReadParticiperByIdMatch($match->getId_Match()))->execute();

        foreach ($participants as $p) {
            if ($p->getId_Joueur() === $idJoueur) {
                $result[] = [
                    'match' => $match,
                    'participer' => $p
                ];
            }
        }
    }

    return $result;
}

/**
 * Calcule les sélections consécutives jusqu’au dernier match
 */
function selectionsConsecutives(array $participations, array $matchs): int
{
    $idsMatchsJoueur = array_map(
        fn($p) => $p['match']->getId_Match(),
        $participations
    );

    $count = 0;
    $sortedMatchs = array_reverse($matchs);

    foreach ($sortedMatchs as $match) {
        if (in_array($match->getId_Match(), $idsMatchsJoueur)) {
            $count++;
        } else {
            break;
        }
    }

    return $count;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques</title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>
<body>
<?php require_once ROOT . '/header.php'; ?>
<div class="container container-wide">
    <div class="card card-wide">

        <h1 class="title-main">Statistiques générales</h1>
        <div class="title-divider"></div>

        <!-- ================= MATCHS ================= -->
        <h2 class="section-title">Résultats des matchs</h2>

        <table class="match-table">
            <thead>
                <tr>
                    <th>Total matchs</th>
                    <th>Total matchs terminés</th>
                    <th>Gagnés</th>
                    <th>Perdus</th>
                    <th>Nuls</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php $totalTermines = $gagnes + $perdus + $nuls;?>
                    <td><?= $totalMatchs ?></td>
                    <td><?= $totalTermines?></td>
                    <td><?= $gagnes ?> (<?= percent($gagnes, $totalTermines) ?>)</td>
                    <td><?= $perdus ?> (<?= percent($perdus, $totalTermines) ?>)</td>
                    <td><?= $nuls ?> (<?= percent($nuls, $totalTermines) ?>)</td>
                </tr>
            </tbody>
        </table>

        <!-- ================= JOUEURS ================= -->
        <h2 class="section-title margin-top-large">Statistiques par joueur</h2>

        <table class="match-table">
            <thead>
                <tr>
                    <th>Joueur</th>
                    <th>Statut</th>
                    <th>Poste préféré</th>
                    <th>Titulaire</th>
                    <th>Remplaçant</th>
                    <th>Moy. éval.</th>
                    <th>% victoires</th>
                    <th>Sélections consécutives</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($joueurs as $joueur):

                $participations = getParticipationsJoueur($joueur->getIdJoueur(), $matchs);

                $titulaire = $remplacant = 0;
                $evals = [];
                $victoires = 0;
                $postes = [];

                foreach ($participations as $p) {
                    $part = $p['participer'];
                    $match = $p['match'];

                    $part->getEst_Titulaire() ? $titulaire++ : $remplacant++;

                    if ($part->getEvaluation()) {
                        $evals[] = $part->getEvaluation();
                    }

                    $postes[] = $part->getPoste();

                    if ($match->getSens_match() === 'GAGNE') {
                        $victoires++;
                    }
                }

                $postePref = !empty($postes)
                    ? Poste::from(array_count_values($postes) === []
                        ? $postes[0]
                        : array_key_first(array_count_values($postes))
                      )->label()
                    : '—';

                $moyenneEval = count($evals) > 0
                    ? round(array_sum($evals) / count($evals), 2)
                    : '—';

                $pctVictoire = count($participations) > 0
                    ? round(($victoires / count($participations)) * 100, 1) . ' %'
                    : '0 %';

                $consecutifs = selectionsConsecutives($participations, $matchs);
            ?>

                <tr>
                    <td><?= htmlspecialchars($joueur->getNomComplet()) ?></td>
                    <td>
                        <span class="badge <?= $joueur->getStatutJoueur() === 'ACTIF' ? 'badge-actif' : 'badge-blesse' ?>">
                            <?= $joueur->getStatutJoueur() ?>
                        </span>
                    </td>
                    <td><?= $postePref ?></td>
                    <td><?= $titulaire ?></td>
                    <td><?= $remplacant ?></td>
                    <td><?= $moyenneEval ?></td>
                    <td><?= $pctVictoire ?></td>
                    <td><?= $consecutifs ?></td>
                </tr>

            <?php endforeach; ?>

            </tbody>
        </table>

    </div>
</div>

</body>
</html>
