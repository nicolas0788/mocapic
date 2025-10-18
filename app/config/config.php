<?php
/**
 * =============================================================================
 * CONFIG.PHP — Configuración global de MOCAPIC (Producción + Desarrollo)
 * =============================================================================
 * 💡 Rol:
 *   - Detecta entorno (Docker, Hostinger u otro).
 *   - Carga variables de entorno desde `.env` si no existen.
 *   - Define rutas absolutas (src, config, public_html).
 *   - Establece constantes globales y entorno de aplicación.
 *   - Prepara configuración de base de datos (sin conectar aún).
 * =============================================================================
 */

declare(strict_types=1);

// -----------------------------------------------------------------------------
// 1️⃣ RUTAS BASE DEL PROYECTO
// -----------------------------------------------------------------------------
define('BASE_PATH', dirname(__DIR__));             // /var/www/html/app
define('SRC_PATH', BASE_PATH . '/src');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public_html');

// -----------------------------------------------------------------------------
// 2️⃣ CARGA DE VARIABLES DE ENTORNO (.env)
// -----------------------------------------------------------------------------
// Este bloque permite que el proyecto funcione tanto en Docker (local)
// como en Hostinger (producción) sin modificar rutas ni archivos.
//
// Prioridad:
//   1. Variables de entorno ya definidas por el sistema.
//   2. Archivo .env en la raíz del proyecto (fuera de public_html).
// -----------------------------------------------------------------------------
if (getenv('APP_ENV') === false) {
    $envCandidates = [
        __DIR__ . '/../../.env',  // raíz del proyecto (recomendado)
        __DIR__ . '/../.env',     // fallback
    ];

    foreach ($envCandidates as $path) {
        if (is_readable($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$key, $value] = array_map('trim', explode('=', $line, 2));
                if (getenv($key) === false) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
            break; // detiene al encontrar el primer .env válido
        }
    }
}

// -----------------------------------------------------------------------------
// 3️⃣ CONFIGURACIÓN GLOBAL DEL PROYECTO
// -----------------------------------------------------------------------------
define('APP_NAME', getenv('APP_NAME') ?: 'MOCAPIC');
define('APP_ENV', getenv('APP_ENV') ?: 'production'); // production | local
define('DEBUG', filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN));
define('TIMEZONE', getenv('APP_TIMEZONE') ?: 'America/Argentina/Buenos_Aires');
date_default_timezone_set(TIMEZONE);

// -----------------------------------------------------------------------------
// 4️⃣ PARÁMETROS DE BASE DE DATOS (para PDO / mysqli)
// -----------------------------------------------------------------------------
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'mocapic');
define('DB_USER', getenv('DB_USER') ?: 'mocapic_user');
define('DB_PASS', getenv('DB_PASS') ?: 'mocapic_pass');

// -----------------------------------------------------------------------------
// 5️⃣ FUNCIONES ÚTILES GLOBALES (opcionales)
// -----------------------------------------------------------------------------
if (!function_exists('app_path')) {
    function app_path(string $path = ''): string {
        return BASE_PATH . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('public_path')) {
    function public_path(string $path = ''): string {
        return PUBLIC_PATH . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('debug_log')) {
    function debug_log(mixed $data): void {
        if (DEBUG) {
            error_log(print_r($data, true));
        }
    }
}

// -----------------------------------------------------------------------------
// 6️⃣ MANEJO DE ERRORES Y EXCEPCIONES (robusto)
// -----------------------------------------------------------------------------
// En modo DEBUG se muestran errores; en producción, se registran en logs.
// -----------------------------------------------------------------------------
if (DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', CONFIG_PATH . '/error.log');
}

// -----------------------------------------------------------------------------
// ✅ CONFIGURACIÓN LISTA
// -----------------------------------------------------------------------------
