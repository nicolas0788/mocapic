<?php
/**
 * =============================================================================
 * Router (App\Core\Router)
 * =============================================================================
 * 💼 Rol:
 *   Clase base del sistema MVC/MVD de MOCAPIC.
 *   - Administra la tabla global de rutas ($routes).
 *   - Recibe registros desde config/routes.php (modularización total).
 *   - Despacha la URL actual al controlador correspondiente.
 *   - Lanza errores 404 o de formato controlados.
 *
 * ⚙️ Convenciones:
 *   - Formato de acción: "Controlador@metodo"
 *   - Los controladores residen en: PUBLIC_PATH/controllers/
 *   - No se usa Composer aún → carga manual con require_once().
 *
 * ⚠️ Reglas de mantenimiento:
 *   🚫 No modificar el nombre de la clase ni de los métodos públicos.
 *   🚫 No borrar las excepciones personalizadas.
 *   ✅ Se pueden ampliar funcionalidades (autoload, middlewares, parámetros).
 * =============================================================================
 */

namespace App\Core;

class Router
{
    /**
     * 📚 Tabla interna de rutas.
     * ----------------------------------------------------------
     * Estructura:
     * [
     *   'GET' => [ '/home' => 'HomeController@index', ... ],
     *   'POST' => [ '/upload' => 'GalleryController@upload', ... ],
     *   'PUT' => [],
     *   'DELETE' => []
     * ]
     * ----------------------------------------------------------
     *
     * 👉 Este array se llena EXTERNAMENTE por config/routes.php
     * usando los métodos públicos: get(), post(), put(), delete().
     */
    private static array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    /* ---------------------------------------------------------------------- */
    /* 1️⃣  REGISTRO DE RUTAS (llamado desde config/routes.php)                */
    /* ---------------------------------------------------------------------- */

    /**
     * Registra una ruta tipo GET.
     * Ejemplo: Router::get('/home', 'HomeController@index');
     */
    public static function get(string $path, string $action): void
    {
        self::$routes['GET'][$path] = $action;
    }

    /**
     * Registra una ruta tipo POST.
     * Ejemplo: Router::post('/upload', 'GalleryController@upload');
     */
    public static function post(string $path, string $action): void
    {
        self::$routes['POST'][$path] = $action;
    }

    /**
     * Registra una ruta tipo PUT (actualizaciones).
     * Ejemplo: Router::put('/user/update/{id}', 'UserController@update');
     */
    public static function put(string $path, string $action): void
    {
        self::$routes['PUT'][$path] = $action;
    }

    /**
     * Registra una ruta tipo DELETE (borrado de recursos).
     * Ejemplo: Router::delete('/gallery/delete/{id}', 'GalleryController@delete');
     */
    public static function delete(string $path, string $action): void
    {
        self::$routes['DELETE'][$path] = $action;
    }

    /* ---------------------------------------------------------------------- */
    /* 2️⃣  DESPACHO (llamado desde index.php)                                */
    /* ---------------------------------------------------------------------- */

    /**
     * =========================================================================
     * DESPACHA LA SOLICITUD ACTUAL
     * =========================================================================
     * Este método busca dentro del array $routes la acción asociada al método
     * y path solicitados, y ejecuta el controlador correspondiente.
     *
     * @param string $method  Método HTTP actual (GET, POST, PUT, DELETE)
     * @param string $path    Path solicitado (ej: "/home", "/gallery")
     *
     * Flujo:
     *  1️⃣ Normaliza método y path.
     *  2️⃣ Busca coincidencia exacta.
     *  3️⃣ Ejecuta el controlador.
     *  4️⃣ Si no hay coincidencia, lanza HttpNotFoundException.
     * =========================================================================
     */
    public static function dispatch(string $method, string $path): void
    {
        // 1️⃣ Normaliza el método HTTP (mayúsculas).
        $method = strtoupper($method);

        // 2️⃣ Normaliza el path (quita / final salvo raíz).
        $path = rtrim($path, '/') ?: '/';

        // 3️⃣ Busca coincidencia exacta en la tabla de rutas.
        if (isset(self::$routes[$method][$path])) {
            $action = self::$routes[$method][$path]; // Ej: "HomeController@index"
            self::execute($action);                  // Ejecuta el controlador
            return;
        }

        // 4️⃣ (Futuro) Soporte de parámetros dinámicos.
        // foreach (self::$routes[$method] as $pattern => $action) {
        //     if (preg_match('#^/user/(\d+)$#', $path, $matches)) {
        //         self::execute($action, ['id' => $matches[1]]);
        //         return;
        //     }
        // }

        // 5️⃣ Si no se encuentra ruta → error controlado.
        throw new HttpNotFoundException("Ruta no encontrada: $method $path");
    }

      /* ---------------------------------------------------------------------- */
    /* 3️⃣  EJECUCIÓN DEL CONTROLADOR/MÉTODO (versión PSR-4)                 */
    /* ---------------------------------------------------------------------- */

    /**
     * Ejecuta el controlador y método asociados a una acción.
     *
     * @param string $action Acción en formato "Controlador@metodo".
     */
    private static function execute(string $action /*, array $params = [] */): void
    {
        // 1️⃣ Valida el formato de la acción (debe contener '@').
        if (strpos($action, '@') === false) {
            throw new \InvalidArgumentException("Ruta mal definida: $action");
        }

        // 2️⃣ Separa nombre de controlador y método.
        [$controllerName, $method] = explode('@', $action, 2);

        // 3️⃣ Construye el nombre completo (namespace) del controlador.
        // Ejemplo: "App\Controllers\HomeController"
        $controllerClass = "App\\Controllers\\{$controllerName}";

        // 4️⃣ Verifica que la clase exista (el autoloader se encarga de incluir el archivo).
        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Controlador no encontrado o clase no definida: {$controllerClass}");
        }

        // 5️⃣ Crea una instancia del controlador.
        $controller = new $controllerClass();

        // 6️⃣ Verifica que el método exista dentro del controlador.
        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Método {$method} no encontrado en {$controllerClass}");
        }

        // 7️⃣ Ejecuta el método del controlador.
        $controller->$method();

        // 🔮 Futuro: aquí podrías manejar parámetros o respuestas personalizadas.
        // Ejemplo:
        // $response = $controller->$method(...$params);
        // echo $response instanceof Response ? $response->render() : $response;
    }

}

/* -------------------------------------------------------------------------- */
/* ⚠️ EXCEPCIÓN PERSONALIZADA: 404                                           */
/* -------------------------------------------------------------------------- */

class HttpNotFoundException extends \Exception {}

/* -------------------------------------------------------------------------- */
/* 📈 PLAN DE EXPANSIÓN                                                       */
/* -------------------------------------------------------------------------- */
/**
 * 🔮 Próximas mejoras posibles:
 * 
 * 1️⃣ Autoload (Composer o manual)
 *     → Reemplazar require_once por namespaces App\Controllers.
 * 2️⃣ Middlewares
 *     → Ejecutar funciones previas a dispatch() (auth, CSRF, logs).
 * 3️⃣ Parámetros dinámicos
 *     → Soporte de /user/{id}, /post/{slug}, etc. con regex.
 * 4️⃣ Clases Request / Response
 *     → Inyectar en controladores para manipular datos HTTP.
 * 5️⃣ Agrupación de rutas
 *     → Router::group('/admin', fn()=>Router::get(...)).
 * 6️⃣ Manejo centralizado de errores
 *     → Envolver dispatch() en try/catch en index.php.
 * 7️⃣ Logging y cacheo de rutas.
 *
 * 🔒 No modificar:
 *  - Nombres públicos (get, post, put, delete, dispatch).
 *  - Propiedad $routes.
 *  - Flujo base de execute().
 */
