<?php
/**
 * =============================================================================
 * routes_admin.php — Panel de administración
 * =============================================================================
 * 💡 Define las rutas accesibles solo para usuarios administradores.
 * =============================================================================
 */

use App\Core\Router;

// Dashboard principal
Router::get('/admin', 'AdminController@index');
// 📄 Vista: views/admin/index.php

// Gestión de usuarios
Router::get('/admin/users', 'AdminController@users');
// 📄 Vista: views/admin/users.php

// Gestión de imágenes
Router::get('/admin/images', 'AdminController@images');
// 📄 Vista: views/admin/images.php
