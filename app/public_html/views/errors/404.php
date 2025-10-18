<?php
/**
 * =============================================================================
 * 404.php — Vista de error "Página no encontrada" (MOCAPIC)
 * =============================================================================
 * 💼 Rol:
 *   - Mostrar un mensaje visual cuando una ruta no coincide con ninguna registrada.
 *   - Mantener el mismo diseño base del sitio (usa layout base si se invoca con render()).
 *
 * 🔗 Interactúa con:
 *   - Core/Router.php (cuando lanza HttpNotFoundException).
 *   - BaseController::render() o directamente `require` desde el index.
 *
 * ⚙️ Qué modificar:
 *   ✅ Cambiar el texto o el diseño del mensaje de error.
 *   ✅ Agregar un enlace de regreso a la página principal.
 *   🚫 No incluir <html> ni <head> si se usa dentro de un layout.
 * =============================================================================
 */
?>

<section class="error-404">
  <h2>404 — Página no encontrada</h2>
  <p>Lo sentimos, la página que buscás no existe o fue movida.</p>
  <a href="/">Volver al inicio</a>
</section>
