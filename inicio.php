<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /coviamu/login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>COVIAMU - Cooperativa de Viviendas</title>
  <link rel="stylesheet" href="public/css/index.css" />
</head>
<body>

  <form action="api/routes/logout.php" method="post" style="position: relative">
    <button type="submit" class="logout-btn">Cerrar sesión</button>
  </form>

  <nav class="floating-nav">
    <ul>
      <li><a href="#">Inicio</a></li>
    </ul>
  </nav>

  <section id="inicio" class="hero">
    <div class="hero-content">
      <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['user']); ?></h1>
    </div>
  </section>

  <section class="relleno"><h2>¿Por qué elegir COVIAMU?</h2><p>Compromiso, comunidad y futuro compartido.</p></section>
  <section class="relleno"><h2>Noticias</h2><p>Últimas novedades sobre el avance de nuestras viviendas.</p></section>
  <section class="relleno"><h2>Contacto</h2><p>Podés escribirnos a info@coviamu.org</p></section>
</body>
</html>
