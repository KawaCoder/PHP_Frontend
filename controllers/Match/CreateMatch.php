<?php
namespace App\Controllers\Match;
use Exception;

class CreateMatch {
    private array $data;

    public function __construct(
        $date_match,
        $nom_equipe_adverse,
        $lieu_de_rencontre,
        $points_subis,
        $points_marques,
        $domiciliation,
        $sens_match
    ) {
        $this->data = [
            'date_match' => $date_match,
            'nom_equipe_adverse' => $nom_equipe_adverse,
            'lieu_de_rencontre' => $lieu_de_rencontre,
            'points_subis' => $points_subis,
            'points_marques' => $points_marques,
            'domiciliation' => $domiciliation,
            'sens_match' => $sens_match
        ];
    }   

    public function execute() {
        $url = API_BACKEND_URL . '/create_match'; // À adapter selon ton URL backend

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n" . "Authorization: Bearer " . ($_SESSION['jwt_token'] ?? '') . "\r\n",
                'method' => 'POST',
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
?>