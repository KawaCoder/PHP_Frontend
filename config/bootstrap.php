<?php
/**
 * Bootstrap du projet (Version AlwaysData)
 */
require_once __DIR__ . '/env.php';

date_default_timezone_set('Europe/Paris');

// ROOT pointe vers la racine du site (/www)
define('ROOT', dirname(__DIR__));

// Comme les fichiers sont directement dans /www/, BASE_URL est vide
define('BASE_URL', '');
define('ASSETS_URL', '/public/assets');

// Chargement de l'autoloader qui est à la racine (/www/Autoloader.php)
require_once ROOT . '/Autoloader.php';
\App\Autoloader::register();

// Gestion des sessions
$sessionLifetime = 1800;
ini_set('session.cookie_lifetime', $sessionLifetime);
ini_set('session.gc_maxlifetime', $sessionLifetime);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header("Location: /views/Auth/LoginView.php");
    exit();
}
$_SESSION['last_activity'] = time();

function isLoggedIn()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: /views/Auth/LoginView.php");
        exit();
    }
}
?>