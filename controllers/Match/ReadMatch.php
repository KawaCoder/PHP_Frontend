<?php
namespace App\Controllers\Match;
use Exception;

class ReadMatch
{
    public function __construct()
    {
    }

    public function execute()
    {
        $url = 'http://localhost:8000/api/read_match'; // À adapter selon ton URL backend

        $token = $_SESSION['jwt_token'] ?? '';
        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\nAuthorization: Bearer " . $token . "\r\n",
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