<?php
namespace App\Controllers\Joueur;

use Exception;

class GetJoueurById
{
    private $id;

    public function __construct($id)
    {
        if (!$id) {
            throw new Exception("ID joueur manquant.");
        }
        $this->id = $id;
    }

    public function execute()
    {
        $url = 'http://localhost:8000/api/joueurs/' . $this->id;

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
