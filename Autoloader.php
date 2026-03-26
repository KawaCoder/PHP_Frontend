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
        $file = __DIR__ . '/' . $path . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }

        // TENTATIVE DE CORRECTION DE CASSE (Gestion des dossiers minuscules : Config -> config)
        // Si le fichier n'est pas trouvé, on essaie de mettre le premier dossier en minuscule
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