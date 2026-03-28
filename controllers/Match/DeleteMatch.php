<?php
namespace App\Controllers\Match;
use Exception;

class DeleteMatch
{
    private string $id_match;

    public function __construct($id_match)
    {
        if (!$id_match) {
            throw new Exception("ID match manquant pour la suppression.");
        }
        $this->id_match = $id_match;
    }

    public function execute()
    {
        $url = 'http://localhost:8000/api/delete_match'; // À adapter selon ton URL backend

        $data = ['id_match' => $this->id_match]; // Sending id_match to match property names

        $token = $_SESSION['jwt_token'] ?? '';
        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\nAuthorization: Bearer " . $token . "\r\n",
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
?>