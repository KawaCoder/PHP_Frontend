<?php
namespace App\Controllers\Match;
use Exception;

class GetMatchById
{
    private string $id_match;

    public function __construct($id_match)
    {
        if (!$id_match) {
            throw new Exception("ID match manquant.");
        }
        $this->id_match = $id_match;
    }

    public function execute()
    {
        $url = 'http://localhost:8000/api/get_match_by_id?id_match=' . urlencode($this->id_match); // À adapter selon ton URL backend

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