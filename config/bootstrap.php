<?php
/**
 * Bootstrap du projet KAST-ASSO
 * 
 * Initialise les constantes globales du projet et charge l'autoloader.
 * Ce fichier garantit la portabilité du projet sur toutes les machines.
 */

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
 * - __DIR__ = /chemin/vers/kast-asso/app/config/
 * - dirname(__DIR__) = /chemin/vers/kast-asso/app/
 * - dirname(dirname(__DIR__)) = /chemin/vers/kast-asso/
 * 
 * Utilisé pour : Inclusions PHP, autoloader, accès aux fichiers système
 */
define('ROOT', dirname(dirname(__DIR__)));

// ========================================
// 2. CONSTANTES BASE_URL et ASSETS_URL (Chemins web)
// ========================================

/**
 * Calcul automatique du chemin web du projet dans l'URL
 * 
 * Problème à résoudre :
 * - Si DocumentRoot pointe vers le dossier parent : BASE_URL = "/kast-asso"
 * - Si DocumentRoot pointe vers le projet lui-même : BASE_URL = ""
 * 
 * Solution :
 * On compare DocumentRoot (racine web du serveur) avec ROOT (racine du projet)
 * pour déduire le préfixe à ajouter dans les URLs.
 * 
 * Exemple 1 (projet dans un sous-dossier) :
 * DocumentRoot : /var/www/
 * ROOT :         /var/www/kast-asso/
 * Résultat :     BASE_URL = "/kast-asso"
 * 
 * Exemple 2 (projet à la racine) :
 * DocumentRoot : /var/www/kast-asso/
 * ROOT :         /var/www/kast-asso/
 * Résultat :     BASE_URL = "" (vide)
 */
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT']);
$projectRoot = realpath(ROOT);

// Soustraction des chemins pour obtenir le chemin relatif web
$relativePath = str_replace($documentRoot, '', $projectRoot);

// Normalisation des séparateurs (Windows utilise \, Linux/Mac utilisent /)
$relativePath = str_replace('\\', '/', $relativePath);

/**
 * BASE_URL : Préfixe pour tous les liens internes du projet (vues PHP, redirections)
 * Utilisé dans : <a href="">, header("Location: "), <form action="">
 */
define('BASE_URL', $relativePath);

/**
 * ASSETS_URL : Préfixe pour tous les fichiers statiques (CSS, JS, images, fonts)
 * Utilisé dans : <link>, <script>, <img>, background-image
 */
define('ASSETS_URL', $relativePath . '/public/assets');

// ========================================
// 3. CHARGEMENT DE L'AUTOLOADER PSR-4
// ========================================

/**
 * L'autoloader permet de charger automatiquement les classes PHP
 * sans avoir besoin de faire require/include manuellement.
 * 
 * Convention : Les namespaces correspondent à l'arborescence des dossiers
 * Exemple : App\Models\CreneauSportif\CreneauSportif
 *           → /app/Models/CreneauSportif/CreneauSportif.php
 */
require ROOT . '/app/Autoloader.php';

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
    $loginUrl = BASE_URL . '/app/views/Auth/LoginView.php';
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
        $loginUrl = BASE_URL . '/app/views/Auth/LoginView.php';
        header("Location: $loginUrl");
        exit();
    }
}
?>