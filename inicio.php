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
  <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
</head>
<body>

  <form action="api/routes/logout.php" method="post">
    <div class='button-left-container'>
      <button type="submit" class="logout-btn"><i class='bx bx-arrow-from-left-stroke'></i>Cerrar sesión</button>
    </div>
  </form>

  <section id="regist-horas" class="panel-content">
    <div>
      <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['user']); ?></h1>
      <div>
        <h3>Registrar horas</h3>
        <p>
          Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum
          Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum 
          Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum
          Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum  
          Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum
          Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum 
        </p>
      </div>
    </div>
  </section>

</body>
</html>
