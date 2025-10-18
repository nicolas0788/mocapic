<?php
/**
 * =============================================================================
 * base.php — Layout principal de la aplicación (MOCAPIC)
 * =============================================================================
 * 💼 Rol:
 *   - Define la estructura HTML común del sitio.
 *   - Envuelve el contenido dinámico de cada vista.
 *
 * 🔗 Interactúa con:
 *   - Cualquier vista (home.php, about.php, etc.).
 *   - Controladores que cargan $content y $title.
 *
 * ⚙️ Qué modificar:
 *   ✅ Editar el <head>, scripts y estilos globales.
 *   ✅ Agregar header/footer/menú común.
 *   🚫 No eliminar la variable <?= $content ?> (inserta la vista).
 * =============================================================================
 */
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'MocaPIC') ?></title>
  <!-- Estilos globales -->
  <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>

  <!-- 🔹 Encabezado global -->
  <header>
    <h1>MocaPIC</h1>
  </header>

  <!-- 🔹 Contenido dinámico de cada página -->
  <main>
    <?= $content ?? '' ?>
  </main>

  <!-- 🔹 Pie de página global -->
  <footer>
    <p>&copy; <?= date('Y') ?> Moca Textil — Todos los derechos reservados.</p>
  </footer>

  <!-- Scripts globales -->
  <script src="/assets/js/main.js" defer></script>
</body>
</html>
