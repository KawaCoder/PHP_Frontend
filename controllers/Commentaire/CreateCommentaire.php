<?php
namespace App\Controllers\Commentaire;
use Exception;
use DateTime;

class CreateCommentaire
{
    private array $data;

    public function __construct($id_joueur, $texte)
    {
        $texte = trim($texte ?? '');
        if (!$id_joueur) {
            throw new Exception("ID joueur manquant pour le commentaire.");
        }
        if ($texte === '') {
            throw new Exception("Le contenu du commentaire ne peut pas être vide.");
        }

        $this->data = [
            'id_joueur' => $id_joueur,
            'commentaire' => $texte,
            'date_commentaire' => (new DateTime())->format('Y-m-d')
        ];
    }

    public function execute()
    {
        $url = 'http://localhost:8000/api/create_commentaire';

        $token = $_SESSION['jwt_token'] ?? '';
        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\nAuthorization: Bearer " . $token . "\r\n",
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
