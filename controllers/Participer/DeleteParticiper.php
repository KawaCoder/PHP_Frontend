<?php
namespace App\Controllers\Participer;
use Exception;

class DeleteParticiper
{
    private array $data;

    public function __construct(
        $id_joueur,
        $id_match
    ) {
        if (!$id_joueur || !$id_match) {
            throw new Exception("ID joueur ou ID match manquant pour la suppression.");
        }

        $this->data = [
            'id_joueur' => $id_joueur,
            'id_match' => $id_match
        ];
    }

    public function execute()
    {
        $url = API_BACKEND_URL . '/delete_participer';

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
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