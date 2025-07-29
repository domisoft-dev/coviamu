<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: /coviamu/admin");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gestion</title>
    <link rel="stylesheet" href="public/css/panel.css">
</head>
<body>

  <form action="api/routes/logout.php" method="post" style="position: relative">
    <button type="submit" class="logout-btn">Cerrar sesión</button>
  </form>

  <section class="panel-content">
      <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['admin']); ?>.</h1>
      <p>Desde acá podés aceptar y/o rechazar usuarios que quieran 
        registrarse en la cooperativa, 
        habilitandolos o no de iniciar sesión.</p>
  </section>

  <section id="users-container">

  </section>

    <script>
    fetch('public/endpointCooperativas.php')
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById('users-container');
        container.innerHTML = '';
        console.log(data);

        data.forEach(user => {
          const div = document.createElement('div');
          div.className = 'user-card';
          div.innerHTML = `
            <p><strong>${user.nombre}</strong> (${user.email})</p>
            ${user.estado !== 'aprobado' ? `
              <button class="btn" onclick="cambiarEstado(${user.id}, 'aprobado')">Aceptar</button>
              <button class="btn" onclick="rechazar(${user.id})">Rechazar</button>
            ` : '<em>Ya aprobado</em>'}
          `;
          container.appendChild(div);
        });
      });

    function cambiarEstado(id, estado) {
      fetch('public/endpointCooperativas.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, estado })
      })
      .then(res => res.json())
      .then(resp => {
        if (resp.success) {
          alert(`Usuario ${estado === 'aprobado' ? 'aprobado' : 'rechazado'}`);
          location.reload();
        } else {
          alert("Error al actualizar el estado");
        }
      });
    }

    function rechazar(id) {
      fetch('public/endpointCooperativas.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
      .then(res => res.json())
      .then(resp => {
        if (resp.success) {
          alert('Usuario eliminado correctamente.');
          location.reload();
        } else {
          alert("Error al actualizar el estado");
        }
      });
    }
  </script>

</body>
</html>
