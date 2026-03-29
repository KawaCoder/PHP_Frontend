<?php
namespace App\Controllers\Joueur;

use Exception;
use DateTime;

class CreateJoueur
{
    private array $data;

    public function __construct(
        $nom_joueur,
        $prenom_joueur,
        $numero_licence,
        $date_naiss,
        $taille,
        $poids,
        $statut_joueur,
        $commentaire
    ) {
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

        // Préparation des données pour l'API
        $this->data = [
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
        $url = API_BACKEND_URL . '/create_joueur'; // À adapter selon ton URL backend

        $token = $_SESSION['jwt_token'] ?? '';
        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\nAuthorization: Bearer " . $token . "\r\n",
                'method' => 'POST',
                'content' => json_encode($this->data),
                'ignore_errors' => true
            ],
        ];

        $context = stream_context_create($options);

        //utiliser curl(surtout pour le backend)
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            throw new Exception("Erreur critique : Impossible de contacter l'API backend.");
        }

        $response = json_decode($result, true);

        // Gestion des erreurs renvoyées par l'API
        if (isset($response['error'])) {
            throw new Exception("Erreur API : " . $response['error']);
        }

        return $response;
    }
}
