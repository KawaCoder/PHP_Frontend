<?php
// PHP_Frontend/utils/ApiClient.php
namespace App\Utils;

require_once __DIR__ . '/../../PHP_Auth/config/security.php';
use Exception;

class ApiClient
{
    /**
     * Effectue une requête vers la passerelle (Gateway) PHP_Auth.
     */
    public static function request(string $endpoint, string $method = 'GET', array $data = [])
    {
        // L'API Gateway (Auth) tourne sur le port 8001 dans notre infrastructure prévue
        $url = 'http://localhost:8001/api/' . ltrim($endpoint, '/');

        $headers = "Content-Type: application/json\r\n" .
                   "Authorization: Bearer " . API_SECRET_TOKEN . "\r\n";

        $options = [
            'http' => [
                'header' => $headers,
                'method' => $method,
                'ignore_errors' => true
            ],
        ];

        if ($method === 'POST') {
            $options['http']['content'] = json_encode($data);
        } else if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            throw new Exception("Erreur critique : Impossible de contacter l'API (PHP_Auth).");
        }

        $response = json_decode($result, true);

        // Gestion des erreurs renvoyées par la Gateway ou le Backend
        if (isset($response['error'])) {
            throw new Exception("Erreur API : " . $response['error']);
        }

        return $response;
    }
}
