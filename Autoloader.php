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
        // 1. Gérer le prefixe "App\"
        $prefix = 'App\\';
        $classRelative = $class;

        if (strpos($class, $prefix) === 0) {
            $classRelative = substr($class, strlen($prefix));
        }

        // 2. Transformer le namespace en chemin de fichier
        $path = str_replace('\\', '/', $classRelative);
        
        // __DIR__ est le dossier racine (/www/)
        $file = __DIR__ . '/' . $path . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }

        // 3. Fallback : Essayer avec le premier dossier en minuscule (ex: Config -> config)
        $parts = explode('/', $path);
        if (count($parts) > 0) {
            $parts[0] = strtolower($parts[0]);
            $fileLower = __DIR__ . '/' . implode('/', $parts) . '.php';
            if (file_exists($fileLower)) {
                require_once $fileLower;
                return;
            }
        }
        
        // 4. Debugging : si on arrive ici, la classe n'est pas trouvée
        // Ne pas faire de die() ou echo ici pour ne pas casser l'autoloading d'autres librairies éventuelles
    }
}
?>