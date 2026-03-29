<?php
// PHP_Frontend/views/Auth/Logout.php
require_once __DIR__ . '/../../config/bootstrap.php';

// Appel HTTP vers l'hébergement Auth pour le logout
$url = API_AUTH_URL . '/logout';
@file_get_contents($url, false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n"
    ]
]));

// Destruction de la session locale sur l'IHM
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();

header('Location: /views/Auth/LoginView.php');
exit();
?>
