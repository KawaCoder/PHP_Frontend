<?php
namespace App\Controllers\Participer;
use Exception;

class ReadParticiperByIdMatch
{
    private $id_match;

    public function __construct($id_match)
    {
        if (!$id_match) {
            throw new Exception("ID match manquant.");
        }
        $this->id_match = $id_match;
    }

    public function execute()
    {
        $url = API_BACKEND_URL . '/read_participer_by_id_match?id_match=' . urlencode($this->id_match);

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