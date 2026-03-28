<?php
namespace App\Controllers\Participer;
use Exception;

class GetParticiperByIds
{
    private $id_joueur;
    private $id_match;

    public function __construct($id_joueur, $id_match)
    {
        if (!$id_joueur || !$id_match) {
            throw new Exception("ID joueur ou ID match manquant.");
        }
        $this->id_joueur = $id_joueur;
        $this->id_match = $id_match;
    }

    public function execute()
    {
        $url = 'http://localhost:8000/api/get_participer_by_ids?id_joueur=' . urlencode($this->id_joueur) . '&id_match=' . urlencode($this->id_match);

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