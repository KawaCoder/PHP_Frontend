<?php
namespace App\Controllers\Joueur;

use Exception;
use DateTime;

class UpdateJoueur
{
    private array $data;
    private $id_joueur;

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

        $this->id_joueur = $id_joueur;
        $this->data = [
            'id_joueur' => $id_joueur,
            'nom_joueur' => $nom_joueur,
            'prenom_joueur' => $prenom_joueur,
            'numero_licence' => $numero_licence,
            'date_naiss' => $date_naiss,
            'taille' => $taille,
            'poids' => $poids,
            'statut_joueur' => $statut_joueur,
            'commentaire' => $commentaire
        ];
    }

    public function execute()
    {
        // Appel API backend
        $url = 'http://localhost:8000/api/joueurs/' . $this->id_joueur;

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'PUT', // On utilise PUT pour la mise à jour
                'content' => json_encode($this->data),
                'ignore_errors' => true
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            throw new Exception("Erreur critique : Impossible de contacter l'API backend.");
        }

        $response = json_decode($result, true);

        if (isset($response['error'])) {
            throw new Exception("Erreur API : " . $response['error']);
        }

        return $response;
    }
}
