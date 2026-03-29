<?php
/**
 * Bootstrap du projet KAST-ASSO
 * 
 * Initialise les constantes globales du projet et charge l'autoloader.
 * Ce fichier garantit la portabilité du projet sur toutes les machines.
 */

// ========================================
// 0. CHARGEMENT DES VARIABLES D'ENVIRONNEMENT (URLs)
// ========================================
require_once __DIR__ . '/env.php';

// ========================================
// 1. CONSTANTE ROOT (Chemin absolu serveur)
// ========================================
date_default_timezone_set('Europe/Paris');

/**
 * ROOT : Chemin absolu vers la racine du projet sur le système de fichiers
 * 
 * Exemple : /var/www/kast-asso/ ou C:/projets/kast-asso/
 * 
 * dirname(dirname(__DIR__)) remonte de 2 niveaux :
/**
 * ROOT : Chemin absolu vers la racine de l'application (Frontend)
 * __DIR__ = /var/www/config
 * dirname(__DIR__) = /var/www
 */
define('ROOT', dirname(__DIR__));

// ========================================
// 2. CONSTANTES BASE_URL et ASSETS_URL (Chemins web)
// ========================================

$documentRoot = realpath($_SERVER['DOCUMENT_ROOT']);
$projectRoot = realpath(ROOT);

// Soustraction des chemins pour obtenir le chemin relatif web
$relativePath = str_replace($documentRoot, '', $projectRoot);
$relativePath = str_replace('\\', '/', $relativePath);

define('BASE_URL', rtrim($relativePath, '/'));
define('ASSETS_URL', BASE_URL . '/public/assets');

// ========================================
// 3. CHARGEMENT DE L'AUTOLOADER PSR-4
// ========================================

require ROOT . '/Autoloader.php';

\App\Autoloader::register();

// ========================================
// 4. GESTION DES SESSIONS ET ACCÈS
// ========================================

// Configuration du timeout de session (30 minutes)
$sessionLifetime = 1800; // 30 minutes en secondes
ini_set('session.cookie_lifetime', $sessionLifetime);
ini_set('session.gc_maxlifetime', $sessionLifetime);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification du timeout de session
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionLifetime)) {
    // Session expirée - déconnexion automatique
    session_unset();
    session_destroy();
    $loginUrl = BASE_URL . '/PHP_Frontend/views/Auth/LoginView.php';
    header("Location: $loginUrl");
    exit();
}

// Mise à jour du timestamp de dernière activité
$_SESSION['last_activity'] = time();

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Redirige vers la page de login si non connecté
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        $loginUrl = BASE_URL . '/views/Auth/LoginView.php';
        header("Location: $loginUrl");
        exit();
    }
}
?>