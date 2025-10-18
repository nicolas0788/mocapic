<?php
/**
 * =============================================================================
 * home.php — Vista principal del sitio (MOCAPIC)
 * =============================================================================
 * 💼 Rol:
 *   - Contiene el contenido específico de la página de inicio.
 *   - Se incrusta dentro del layout base (base.php).
 *
 * 🔗 Interactúa con:
 *   - HomeController@index → que carga esta vista.
 *   - base.php → layout que la envuelve.
 *
 * ⚙️ Qué modificar:
 *   ✅ Agregar HTML, componentes, imágenes, textos, etc.
 *   ✅ Usar variables enviadas por el controlador ($title, $hero, etc.).
 *   🚫 No incluir <html>, <head> ni <body> (eso lo hace el layout).
 * =============================================================================
 */
?>

<h1><?= htmlspecialchars($message) ?></h1>
<p>Ruta cargada correctamente desde <strong>Router → Controller → View</strong>.</p>