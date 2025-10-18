<?php
/**
 * =============================================================================
 * routes_api.php — Endpoints API (Back-End / AJAX)
 * =============================================================================
 * 💡 Incluye rutas POST/GET utilizadas por JS o formularios:
 *     - Subida de imágenes
 *     - Autenticación
 *     - Eliminación de imágenes
 * =============================================================================
 */

use App\Core\Router;

// Subir imagen
Router::post('/api/upload', 'GalleryController@upload');
// 📦 Espera un formulario con enctype="multipart/form-data".

// Eliminar imagen (solo usuarios con permisos)
Router::post('/api/delete', 'AdminController@delete');

// Login / Logout
Router::post('/api/auth/login', 'AuthController@login');
Router::get('/api/auth/logout', 'AuthController@logout');
