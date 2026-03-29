<?php
namespace App\Controllers\Participer;
use Exception;

class ReadParticiperByIdJoueur {
    private $id_joueur;

    public function __construct($id_joueur) {
        if (!$id_joueur) {
            throw new Exception("ID joueur manquant.");
        }
        $this->id_joueur = $id_joueur;
    }   

    public function execute() {
        $url = API_BACKEND_URL . '/read_participer_by_id_joueur?id_joueur=' . urlencode($this->id_joueur);

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n" . "Authorization: Bearer " . ($_SESSION['jwt_token'] ?? '') . "\r\n",
                'method' => 'GET',
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
?>