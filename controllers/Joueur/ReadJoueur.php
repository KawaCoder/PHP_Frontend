<?php
namespace App\Controllers\Joueur;

use Exception;

class ReadJoueur
{
    public function __construct()
    {
    }

    public function execute()
    {
        $url = 'http://localhost:8000/api/read_joueur'; // À adapter selon ton URL backend

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
            throw new Exception("Erreur critique : Impossible de contacter l'API backend pour récupérer les joueurs.");
        }

        $response = json_decode($result, true);

        if (isset($response['error'])) {
            throw new Exception("Erreur API : " . $response['error']);
        }

        return $response;
    }
}
