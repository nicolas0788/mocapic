<?php
/**
 * =============================================================================
 * routes_front.php — Rutas públicas del sitio (Front-End)
 * =============================================================================
 * 💡 Contiene las rutas accesibles al visitante (home, galería, contacto, etc.)
 * =============================================================================
 */

use App\Core\Router;

// Página principal (inicio)
Router::get('/', 'HomeController@index');
// 🧭 Controlador: HomeController → método: index()
// 📄 Vista: views/home.php

// Página "Acerca de" (opcional)
Router::get('/about', 'HomeController@about');
// 🧭 Controlador: HomeController → método: about()
// 📄 Vista: views/about.php

// Galería pública de imágenes
Router::get('/galeria', 'GalleryController@index');
// 🧭 Controlador: GalleryController → método: index()
// 📄 Vista: views/gallery.php
