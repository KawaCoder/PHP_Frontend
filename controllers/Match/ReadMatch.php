<?php
namespace App\Controllers\Match;
use Exception;

class ReadMatch {
    public function __construct() {
    }

    public function execute() {
        $url = API_BACKEND_URL . '/read_match'; // À adapter selon ton URL backend

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'GET',
                'ignore_errors' => true
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            throw new Exception("Erreur critique : Impossible de contacter l'API backend pour récupérer les matchs.");
        }

        $response = json_decode($result, true);

        if (isset($response['error'])) {
            throw new Exception("Erreur API : " . $response['error']);
        }

        return $response;
    }
}
?>