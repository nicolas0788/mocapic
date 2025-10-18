<?php
/**
 * =============================================================================
 * Autoloader.php — Cargador automático de clases (MOCAPIC)
 * =============================================================================
 * 📂 Ruta: app/src/Core/Autoloader.php
 * =============================================================================
 * 💡 Rol:
 *   - Carga clases automáticamente sin depender de Composer.
 *   - Implementa estándar PSR-4 para el namespace `App\`.
 *   - No depende de constantes externas (funciona en cualquier entorno).
 *
 * 🔗 Interactúa con:
 *   - index.php → se registra con Autoloader::register()
 *   - Carga automática de controladores, modelos, etc.
 *
 * 🧰 Beneficios:
 *   ✅ Portátil entre entornos (Docker, XAMPP, Hostinger)
 *   ✅ Menos dependencias del entorno
 *   ✅ Compatible con futura adopción de Composer
 * =============================================================================
 */

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Autoloader
{
    /**
     * Registra el autoloader en el stack SPL.
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    /**
     * Carga automática de clases PSR-4 (namespace → carpeta).
     *
     * @param string $class Ejemplo: "App\Core\Router"
     * @throws RuntimeException Si el archivo no existe.
     */
    private static function autoload(string $class): void
    {
        /* ------------------------------------------------------------------ */
        /* 1️⃣ Namespace base del proyecto                                    */
        /* ------------------------------------------------------------------ */
        $prefix = 'App\\';

        // Solo procesar clases dentro del namespace App\
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            return;
        }

        /* ------------------------------------------------------------------ */
        /* 2️⃣ Resolver directorio base dinámicamente                         */
        /* ------------------------------------------------------------------ */
        // __DIR__ = /var/www/html/app/src/Core
        // dirname(__DIR__, 2) = /var/www/html/app
        // Luego agregamos '/src/' para llegar a la raíz del código fuente.
        $baseDir = dirname(__DIR__, 2) . '/src/';

        /* ------------------------------------------------------------------ */
        /* 3️⃣ Convertir el namespace a ruta del archivo                      */
        /* ------------------------------------------------------------------ */
        $relativeClass = substr($class, strlen($prefix));           // "Core/Router"
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        /* ------------------------------------------------------------------ */
        /* 4️⃣ Cargar el archivo si existe                                    */
        /* ------------------------------------------------------------------ */
        if (is_file($file)) {
            require_once $file;
            self::log("Clase cargada: {$class} → {$file}");
            return;
        }

        /* ------------------------------------------------------------------ */
        /* 5️⃣ Lanzar excepción si no se encuentra                            */
        /* ------------------------------------------------------------------ */
        $msg = "[Autoload] No se encontró la clase {$class} en {$file}";
        error_log($msg);
        throw new RuntimeException($msg);
    }

    /**
     * Logging condicional (solo si DEBUG está activado).
     */
    private static function log(string $message): void
    {
        if (defined('DEBUG') && DEBUG) {
            error_log("[Autoloader] " . $message);
        }
    }
}
