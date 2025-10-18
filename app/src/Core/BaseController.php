<?php
/**
 * =============================================================================
 * BaseController — Clase padre de todos los controladores (MOCAPIC)
 * =============================================================================
 * 💼 Rol:
 *   - Centralizar métodos y utilidades comunes a todos los controladores.
 *   - Evitar duplicar código (renderización, redirección, respuestas JSON).
 *
 * 🔗 Interactúa con:
 *   - Los demás controladores (HomeController, GalleryController, etc.).
 *   - Las vistas (renderiza plantillas y layouts).
 *   - El Router (quien instancia cada controlador).
 *
 * ⚙️ Qué modificar:
 *   ✅ Podés agregar más métodos (log, validar, autenticar, etc.).
 *   ✅ Podés cambiar el layout por defecto desde render().
 *   🚫 No cambiar el nombre de la clase ni los métodos públicos existentes
 *      (render, json, redirect) para mantener compatibilidad.
 * =============================================================================
 */

namespace App\Core;

class BaseController
{
    /**
     * -------------------------------------------------------------------------
     * render() — Renderizar una vista dentro del layout base
     * -------------------------------------------------------------------------
     * 📋 Propósito:
     *   - Cargar una vista (ej. 'home.php') con variables opcionales.
     *   - Insertar su contenido dentro del layout general ('layouts/base.php').
     *
     * @param string $view   Nombre del archivo de vista (p. ej. 'home.php')
     * @param array  $vars   Variables a pasar a la vista (compactadas)
     * @param string $layout Layout principal a usar (por defecto 'layouts/base.php')
     * -------------------------------------------------------------------------
     */
    protected function render(string $view, array $vars = [], string $layout = 'layouts/base.php'): void
    {
        // 🔹 Extrae variables del array $vars para uso directo en la vista.
        extract($vars);

        // 🔹 Determina las rutas reales de vista y layout.
        $viewPath   = PUBLIC_PATH . '/views/' . ltrim($view, '/') . '.php';
        $layoutPath = PUBLIC_PATH . '/views/' . ltrim($layout, '/');

        // 🔹 Verifica que los archivos existan antes de incluirlos.
        if (!is_file($viewPath)) {
            throw new \RuntimeException("Vista no encontrada: {$viewPath}");
        }
        if (!is_file($layoutPath)) {
            throw new \RuntimeException("Layout no encontrado: {$layoutPath}");
        }

        // 🔹 Inicia el buffer de salida para capturar el contenido de la vista.
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // 🔹 Incluye el layout principal (que debe usar la variable $content).
        require $layoutPath;
    }

    /**
     * -------------------------------------------------------------------------
     * json() — Enviar respuesta JSON al cliente - Para peticiones FETCH
     * -------------------------------------------------------------------------
     * 📋 Propósito:
     *   - Generar una respuesta JSON estructurada (ideal para APIs).
     *
     * @param mixed $data    Datos a codificar.
     * @param int   $status  Código de estado HTTP (por defecto 200).
     * -------------------------------------------------------------------------
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * -------------------------------------------------------------------------
     * redirect() — Redirigir al usuario a otra ruta o URL
     * -------------------------------------------------------------------------
     * 📋 Propósito:
     *   - Redirige de forma segura al usuario hacia una ruta interna o externa.
     *   - Útil después de acciones como login/logout o guardado de formularios.
     * -------------------------------------------------------------------------
     */
    protected function redirect(string $url, int $code = 302): void
    {
        // 1️⃣ Validación del parámetro $url
        if (trim($url) === '') {
            throw new \InvalidArgumentException('redirect() recibió una URL vacía.');
        }

        // 2️⃣ Determinar si la URL es externa o interna
        $isExternal = preg_match('/^https?:\/\//i', $url);

        // 3️⃣ Si es interna, se concatena con BASE_URL (si existe)
        if (!$isExternal) {
            $url = '/' . ltrim($url, '/');
            $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
            $url = $base . $url;
        }

        // 4️⃣ Definir código HTTP y enviar cabecera Location
        http_response_code($code);
        header("Location: {$url}");
        exit;
    }
}
