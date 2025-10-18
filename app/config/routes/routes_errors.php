<?php
/**
 * =============================================================================
 * routes_errors.php — Páginas de error personalizadas
 * =============================================================================
 * 💡 Muestra vistas personalizadas para errores 404 y 500.
 * =============================================================================
 */

use App\Core\Router;

// Página 404 (no encontrada)
Router::get('/404', 'ErrorController@notFound');

// Página 500 (error interno del servidor)
Router::get('/500', 'ErrorController@serverError');
