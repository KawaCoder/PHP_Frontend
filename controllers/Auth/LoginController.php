<?php
namespace App\Controllers\Auth;

use Exception;

class LoginController
{
    private $username;
    private $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function execute()
    {
        $url = 'http://localhost:8001'; // URL de l'API Auth
        
        $data = [
            'username' => $this->username,
            'password' => $this->password
        ];

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

        if ($result === false) {
            return false;
        }

        $response = json_decode($result, true);

        // Si l'API Auth renvoie un token valide
        if (isset($response['token'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['jwt_token'] = $response['token'];
            $_SESSION['admin_username'] = $this->username;
            return true;
        }

        return false;
    }
}
?>
