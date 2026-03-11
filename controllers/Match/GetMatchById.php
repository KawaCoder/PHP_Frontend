<?php
namespace App\Controllers\Match;
use App\Models\Match\Match_;
use App\Models\Match\MatchDAO;
use Exception;

class GetMatchById {

    private string $id_match;

    public function __construct($id_match) {
        $this->id_match = $id_match;
    }

    public function execute() {
        $url = 'http://localhost:8000/api/match/' . $this->id_match;

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "Content-Type: application/json\r\n",
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