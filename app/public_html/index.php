<?php
/**
 * =============================================================================
 * index.php — Front Controller (MOCAPIC)
 * =============================================================================
 * 💼 Rol:
 *   - Punto de entrada único del sitio (patrón MVC).
 *   - Configura entorno, seguridad, autoload y enrutamiento.
 *   - Dirige cada solicitud HTTP al controlador correspondiente.
 *
 * 🔗 Interactúa con:
 *   - app/src/Core/Autoloader.php → carga clases automáticamente.
 *   - app/src/Core/Router.php → gestiona rutas y controladores.
 *   - app/config/routes.php → registra las rutas de la app.
 *
 * 🧱 Requisitos:
 *   - PHP 8.1+
 *   - Apache con mod_rewrite (todas las URLs redirigen a este archivo).
 * =============================================================================
 */

declare(strict_types=1); // 1️⃣ Modo estricto: evita errores por tipos ambiguos.

/* -------------------------------------------------------------------------- */
/*  2) Entorno base y configuración inicial                                   */
/* -------------------------------------------------------------------------- */

// 2.1) Zona horaria y cabeceras mínimas
date_default_timezone_set('America/Argentina/Buenos_Aires');
header('X-Frame-Options: SAMEORIGIN'); // Evita iframes externos maliciosos
header('X-Content-Type-Options: nosniff');

// 2.2) Constantes base del proyecto
define('ROOT_PATH', dirname(__DIR__));               // /var/www/html
define('APP_PATH', ROOT_PATH . '/app');              // /var/www/html/app
define('SRC_PATH', APP_PATH . '/src');               // /var/www/html/app/src
define('CONFIG_PATH', APP_PATH . '/config');         // /var/www/html/app/config
define('CONTROLLERS_PATH', SRC_PATH . '/Controllers'); // /var/www/html/app/src/Controllers
define('PUBLIC_PATH', __DIR__);                      // /var/www/html/public_html
define('DEBUG', true);                               // Cambiar a false en producción

/* -------------------------------------------------------------------------- */
/*  3) Sesión segura                                                          */
/* -------------------------------------------------------------------------- */

if (session_status() !== PHP_SESSION_ACTIVE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    if (empty($_COOKIE['mocapic_sid'])) {
        ini_set('session.name', 'mocapic_sid');
    }

    ini_set('session.use_strict_mode', '1');

    session_start();

    $_SESSION['csrf'] = $_SESSION['csrf'] ?? bin2hex(random_bytes(20));
}

/* -------------------------------------------------------------------------- */
/*  4) Autoloader profesional                                                 */
/* -------------------------------------------------------------------------- */
/**
 * Carga clases automáticamente siguiendo el esquema PSR-4 (App\... → /app/src/...).
 * Elimina los require manuales de Router, Controladores y Vistas.
 */
require_once dirname(__DIR__) . '/src/Core/Autoloader.php';
App\Core\Autoloader::register();

/* -------------------------------------------------------------------------- */
/*  5) Configuración de errores según entorno                                 */
/* -------------------------------------------------------------------------- */

if (DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

/* -------------------------------------------------------------------------- */
/*  6) Router + registro de rutas                                             */
/* -------------------------------------------------------------------------- */

use App\Core\Router;

// ✅ Ya no es necesario require Router.php ni View.php manualmente (autoloader los carga).
require_once __DIR__ . '/../config/routes.php';

/* -------------------------------------------------------------------------- */
/*  7) Despacho de la solicitud actual + manejo global de errores             */
/* -------------------------------------------------------------------------- */

try {
    // Obtener datos de la solicitud actual
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Despachar la ruta
Router::dispatch( $method, $uri );

} catch (\App\Core\Exceptions\NotFoundException $e) {
    // ⚠️ Error 404 (ruta no encontrada)
    http_response_code(404);

    if (DEBUG) {
        echo "<h1>404 — Página no encontrada</h1>";
        echo "<pre>{$e->getMessage()}</pre>";
    } else {
        // Vista de error genérica (si la tenés en views/errors/404.php)
        include_once APP_PATH . '/views/errors/404.php';
    }

} catch (Throwable $e) {
    // ⚠️ Error 500 (excepciones generales)
    http_response_code(500);

    if (DEBUG) {
        echo "<h1>500 — Error interno del servidor</h1>";
        echo "<pre>{$e->getMessage()}\n\n{$e->getTraceAsString()}</pre>";
    } else {
        // Vista de error genérica (si la tenés en views/errors/500.php)
        include_once APP_PATH . '/views/errors/500.php';
    }
}
