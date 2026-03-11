<?php
namespace App\Controllers\Match;
use App\Models\Match\Match_;
use App\Models\Match\MatchDAO;
use Exception;

class UpdateMatch {

    private $id_match;
    private $date_match;
    private $nom_equipe_adverse;
    private $lieu_de_rencontre;
    private $points_subis;
    private $points_marques;
    private $domiciliation;
    private $sens_match;

    public function __construct(
        $id_match,
        $date_match,
        $nom_equipe_adverse,
        $lieu_de_rencontre,
        $points_subis,
        $points_marques,
        $domiciliation,
        $sens_match
    ) {

        $this->data = [
            'id' => $id_match,
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
        $url = 'http://localhost:8000/api/match/' . $this->data['id'];

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
?>