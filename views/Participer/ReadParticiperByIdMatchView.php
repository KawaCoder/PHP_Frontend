<?php
namespace App\Views\Participer;

require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\Participer\ReadParticiperByIdMatch;
use App\Controllers\Joueur\GetJoueurById;
use App\Enums\Poste;

requireLogin();

if (!isset($_GET['id_match'])) {
    header("Location: index.php");
    exit();
}

$id_match = (int) $_GET['id_match'];

$participants = (new ReadParticiperByIdMatch($id_match))->execute();

$titulaires = [];
$remplacants = [];

foreach ($participants as $participant) {
    if ($participant->getEst_Titulaire()) {
        $titulaires[$participant->getPoste()] = $participant;
    } else {
        $remplacants[] = $participant;
    }
}

$nbTitulaires = count($titulaires);
$nbRemplacants = count($remplacants);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sélection de l'équipe</title>
    <link rel="stylesheet" href="/public/css/main.css">
</head>
<body>
<?php require_once __DIR__ . '/../../../header.php'; ?>
<div class="container container-wide">
    <div class="card card-wide">

        <h1 class="title-main">Sélection de l'équipe du match</h1>
        <div class="title-divider"></div>

        <?php
            // Vérifie combien de titulaires ne sont pas ACTIF
            $nbInactifs = 0;
            foreach ($titulaires as $participant) {
                $joueurController = new GetJoueurById($participant->getId_Joueur());
                $joueur = $joueurController->execute();
                if (strtoupper($joueur->getStatutJoueur()) !== 'ACTIF') {
                    $nbInactifs++;
                }
            }

            // Déterminer le message et la classe
            if ($nbTitulaires < 15) {
                $teamMessage = "Équipe incomplète ($nbTitulaires / 15 titulaires)";
                $teamClass = "team-status-warning";
            } elseif ($nbInactifs > 0) {
                $teamMessage = "Tous les titulaires ne sont pas ACTIF ($nbInactifs joueur(s) inactif(s))";
                $teamClass = "team-status-warning";
            } else {
                $teamMessage = "Équipe complète";
                $teamClass = "team-status-ok";
            }
            ?>
            <div class="team-status <?= $teamClass ?>">
                <?= $teamMessage ?>
            </div>


        <!-- TITULAIRES -->
        <h2 class="section-title">Titulaires</h2>
        <div class="player-grid">
            <?php foreach ($titulaires as $poste => $participant):
                $joueurController = new GetJoueurById($participant->getId_Joueur());
                $joueur = $joueurController->execute();
                $statut = strtoupper($joueur->getStatutJoueur());
            ?>
                <div class="player-card selected">
                    <div class="player-card-content">

                        <h3 class="player-card-name">
                            <?= htmlspecialchars($joueur->getNomComplet()) ?>
                        </h3>

                        <?php if ($statut == 'ACTIF'): ?>
                            <span class="badge badge-actif"><?= htmlspecialchars($statut) ?></span>
                        <?php elseif ($statut == 'BLESSE'): ?>
                            <span class="badge badge-blesse"><?= htmlspecialchars($statut) ?> /!\</span>
                        <?php elseif ($statut == 'SUSPENDU'): ?>
                            <span class="badge badge-suspendu"><?= htmlspecialchars($statut) ?> /!\</span>
                        <?php elseif ($statut == 'ABSENT'): ?>
                            <span class="badge badge-blesse"><?= htmlspecialchars($statut) ?> /!\</span>
                        <?php endif; ?>

                        <div class="player-card-info">
                            <span><strong>Poste :</strong> <?= htmlspecialchars(Poste::from($poste)->label()) ?></span>
                            <span><strong>Taille :</strong> <?= $joueur->getTaille() ?> cm</span>
                            <span><strong>Poids :</strong> <?= $joueur->getPoids() ?> kg</span>
                            <span><strong>Évaluation :</strong> <?= $participant->getEvaluation() ?: '—' ?></span>
                        </div>

                        <div class="button-group margin-top-small">
                            <a href="/app/views/Participer/UpdateParticiperView.php?id_match=<?= $id_match ?>&id_joueur=<?= $participant->getId_Joueur() ?>">
                                <button class="button button-primary">Modifier</button>
                            </a>
                        </div>

                        <div class="button-group margin-top-small">
                            <a href="/app/views/Participer/DeleteParticiperView.php?id_match=<?= $id_match ?>&id_joueur=<?= $participant->getId_Joueur() ?>">
                                <button class="button button-secondary">Supprimer</button>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($nbTitulaires == 0): ?>
            <p>Pas de titulaire sélectionné</p>
        <?php endif; ?>

        <!-- REMPLAÇANTS -->
        <h2 class="section-title">Remplaçants</h2>
        <div class="player-grid">
            <?php foreach ($remplacants as $participant):
                $joueurController = new GetJoueurById($participant->getId_Joueur());
                $joueur = $joueurController->execute();
                $statut = strtoupper($joueur->getStatutJoueur());
            ?>
                <div class="player-card">
                    <div class="player-card-content">

                        <h3 class="player-card-name">
                            <?= htmlspecialchars($joueur->getNomComplet()) ?>
                        </h3>

                        <?php if ($statut == 'ACTIF'): ?>
                            <span class="badge badge-actif"><?= htmlspecialchars($statut) ?></span>
                        <?php elseif ($statut == 'BLESSE'): ?>
                            <span class="badge badge-blesse"><?= htmlspecialchars($statut) ?> /!\</span>
                        <?php elseif ($statut == 'SUSPENDU'): ?>
                            <span class="badge badge-suspendu"><?= htmlspecialchars($statut) ?> /!\</span>
                        <?php elseif ($statut == 'ABSENT'): ?>
                            <span class="badge badge-blesse"><?= htmlspecialchars($statut) ?> /!\</span>
                        <?php endif; ?>

                        <div class="player-card-info">
                            <span><strong>Poste :</strong> <?= htmlspecialchars(Poste::from($participant->getPoste())->label()) ?></span>
                            <span><strong>Taille :</strong> <?= $joueur->getTaille() ?> cm</span>
                            <span><strong>Poids :</strong> <?= $joueur->getPoids() ?> kg</span>
                            <span><strong>Évaluation :</strong> <?= $participant->getEvaluation() ?: '—' ?></span>
                        </div>

                        <div class="button-group margin-top-small">
                            <a href="/app/views/Participer/UpdateParticiperView.php?id_match=<?= $id_match ?>&id_joueur=<?= $participant->getId_Joueur() ?>">
                                <button class="button button-primary">Modifier</button>
                            </a>
                        </div>

                        <div class="button-group margin-top-small">
                            <a href="/app/views/Participer/DeleteParticiperView.php?id_match=<?= $id_match ?>&id_joueur=<?= $participant->getId_Joueur() ?>">
                                <button class="button button-secondary">Supprimer</button>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($nbRemplacants == 0): ?>
            <p>Pas de remplaçant sélectionné</p>
        <?php endif; ?>

        <!-- ACTIONS GLOBALES -->
        <div class="button-group margin-top-large">
            <a href="/app/views/Participer/CreateParticiperView.php?id_match=<?= $id_match ?>">
                <button class="button button-primary">Ajouter un joueur</button>
            </a>

            <a href="/app/views/Match/ReadMatchView.php">
                <button class="button button-secondary">Retour</button>
            </a>
        </div>

    </div>
</div>

</body>
</html>
