<?php
namespace App;

class Autoloader
{
    public static function register()
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    public static function autoload($class)
    {

        // Vérifie que la classe appartient bien au namespace App\
        $prefix = __NAMESPACE__ . '\\';

        if (strpos($class, $prefix) === 0) {
            // Retirer App\ du namespace
            $class = substr($class, strlen($prefix));
        }

        // Remplacer \ par / pour le chemin du fichier
        $path = str_replace('\\', '/', $class);
        
        // 1. Essayer dans PHP_Frontend en priorité
        $fileFrontend = __DIR__ . '/' . $path . '.php';
        if (file_exists($fileFrontend)) {
            require $fileFrontend;
            return;
        }

        // 2. Essayer dans PHP_Auth
        $fileAuth = dirname(__DIR__) . '/PHP_Auth/' . $path . '.php';
        if (file_exists($fileAuth)) {
            require $fileAuth;
            return;
        }

        // 3. Essayer dans PHP_Backend (si besoin, comme pour les modèles partagés)
        $fileBackend = dirname(__DIR__) . '/PHP_Backend/' . $path . '.php';
        if (file_exists($fileBackend)) {
            require $fileBackend;
            return;
        }

        // TENTATIVE DE CORRECTION DE CASSE pour PHP_Frontend
        $parts = explode('/', $path);
        if (count($parts) > 0) {
            $parts[0] = strtolower($parts[0]);
            $fileLower = __DIR__ . '/' . implode('/', $parts) . '.php';
            if (file_exists($fileLower)) {
                require $fileLower;
            }
        }
    }
}
?>