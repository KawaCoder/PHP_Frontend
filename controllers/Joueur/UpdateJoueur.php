<?php
namespace App\Controllers\Joueur;

use App\Models\Joueur\Joueur;
use App\Models\Joueur\JoueurDAO;
use Exception;
use DateTime;

class UpdateJoueur
{
    private Joueur $joueur;

    public function __construct(
        $id_joueur,
        $nom_joueur,
        $prenom_joueur,
        $numero_licence,
        $date_naiss,
        $taille,
        $poids,
        $statut_joueur,
        $commentaire
    ) {
        if (!$id_joueur) {
            throw new Exception("ID joueur manquant.");
        }

        // Validation
        if ($nom_joueur === '' || $prenom_joueur === '' || $numero_licence === '') {
            throw new Exception('Nom, Prénom et Licence sont obligatoires.');
        }

        // Validation DATE DE NAISSANCE
        if ($date_naiss) {
            $d = DateTime::createFromFormat('Y-m-d', $date_naiss);
            if (!$d || $d->format('Y-m-d') !== $date_naiss) {
                throw new Exception('Date de naissance invalide.');
            }
            if ($d > new DateTime()) {
                throw new Exception('La date de naissance ne peut pas être dans le futur.');
            }
        }

        // Validation TAILLE
        if ($taille !== null) {
            if ($taille <= 0 || $taille > 300) {
                throw new Exception('La taille doit être comprise entre 0 et 300 cm.');
            }
        }

        // Validation POIDS
        if ($poids !== null && ($poids <= 0 || $poids > 200)) {
            throw new Exception('Le poids doit être compris entre 0 et 200 kg.');
        }

        // Création de l'objet Joueur
        $joueur = new Joueur();
        $joueur->setIdJoueur($id_joueur);
        $joueur->setNomJoueur($nom_joueur);
        $joueur->setPrenomJoueur($prenom_joueur);
        $joueur->setNumeroLicence($numero_licence);
        $joueur->setDateNaiss($date_naiss);
        $joueur->setTaille($taille);
        $joueur->setPoids($poids);
        $joueur->setStatutJoueur($statut_joueur);
        $joueur->setCommentaire($commentaire);

        $this->joueur = $joueur;
    }

    public function execute()
    {
        $dao = new JoueurDAO();
        return $dao->update($this->joueur);
    }
}
