<?php
/**
 * =============================================================================
 * routes.php — Registro maestro de rutas (MOCAPIC)
 * =============================================================================
 * 💡 Rol:
 *   - Reúne todos los módulos de rutas organizados en /config/routes/.
 *   - Permite mantener la app limpia y modular.
 *   - Cada subarchivo define rutas específicas (front, api, admin, etc.).
 *
 * ⚙️ Flujo:
 *   index.php → Router.php → (este archivo) → subrutas → Controladores
 * =============================================================================
 */

use App\Core\Router;

// -----------------------------------------------------------------------------
// 1) Rutas públicas del sitio
// -----------------------------------------------------------------------------
require_once __DIR__ . '/routes/routes_front.php';

// -----------------------------------------------------------------------------
// 2) Endpoints API (uploads, auth, etc.)
// -----------------------------------------------------------------------------
require_once __DIR__ . '/routes/routes_api.php';

// -----------------------------------------------------------------------------
// 3) Rutas del panel administrativo
// -----------------------------------------------------------------------------
require_once __DIR__ . '/routes/routes_admin.php';

// -----------------------------------------------------------------------------
// 4) Páginas de error personalizadas
// -----------------------------------------------------------------------------
require_once __DIR__ . '/routes/routes_errors.php';

/**
 * ✅ Buenas prácticas:
 * - No agregar rutas directamente aquí: siempre en los subarchivos.
 * - Si la app crece, podés añadir más secciones (ej. /premium/, /user/, etc.)
 * - No cerrar con "?>" (evita problemas de salida accidental).
 */
