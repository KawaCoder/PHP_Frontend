<?php
namespace App\Controllers\Joueur;

use Exception;

class DeleteJoueur
{
    private $id;

    public function __construct($id)
    {
        if (!$id) {
            throw new Exception("ID joueur manquant pour la suppression.");
        }
        $this->id = $id;
    }

    public function execute()
    {
        $url = 'http://localhost:8000/api/delete_joueur'; // À adapter selon ton URL backend

        $data = ['id' => $this->id];

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($data),
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
