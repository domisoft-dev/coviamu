<?php
/*
================================================================================
* Project:       https://github.com/domisoft-dev/coviamu
* File:          panel.php
* Author:        domisoft-dev
* Description:   Panel de gestión para administradores de COVIAMU.
*                Permite aprobar o rechazar usuarios registrados y visualizar sus datos.
================================================================================
* Librerías:
* - Boxicons (https://cdn.boxicons.com)
* - SweetAlert2 (https://cdn.jsdelivr.net/npm/sweetalert2@11)
================================================================================
* Sections Overview:
* - PHP: manejo de sesión y redirección si no hay admin logueado
* - HTML: estructura de la página, formularios de logout, secciones de contenido y tabla de usuarios
* - JS: fetch de usuarios desde endpointCooperativas.php
*       - Funciones cambiarEstado y rechazar para actualizar o eliminar usuarios
*       - Creación dinámica de tabla de usuarios
*       - Feedback con SweetAlert2
================================================================================
*/


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
    <link rel="stylesheet" href="public/css/main.css">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

  <form action="api/routes/logout.php" method="post">
    <div class='button-left-container'>
      <button type="submit" class="logout-btn"><i class='bx bx-arrow-from-left-stroke'></i> Cerrar sesión</button>
    </div>
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

    // Crear tabla
    const table = document.createElement('table');
    table.className = 'user-table';
    table.innerHTML = `
      <thead>
        <tr>
          <th></th>
          <th>ID</th>
          <th>Nombre</th>
          <th>Mail</th>
          <th>Horas trabajadas</th>
          <th>Comprobantes</th>
        </tr>
      </thead>
      <tbody></tbody>
    `;

    const tbody = table.querySelector('tbody');

    data.forEach(user => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        ${user.estado !== 'aprobado' ? `
        <button class="btnAceptar" onclick="cambiarEstado(${user.id}, 'aprobado')">Aceptar</button>
        <button class="btnRechazar" onclick="rechazar(${user.id})">Rechazar</button>
        ` : '<em>Usuario aprobad@</em>'}

        <td>${user.id}</td>
        <td>${user.nombre}</td>
        <td>${user.email}</td>
        <td>
          <p>${user.horas}</p>
        </td>
        <td>
          ${
            user.recibo
              ? `<a href="public/uploads/recibos/DSFT-ID${user.id}/${user.recibo}" target="_blank">
              <button class="btn">Ver Recibo</button>
              </a>`
              : `<button class="btn" onclick="Swal.fire({
                  icon: 'info',
                  title: 'Sin recibo',
                  text: 'El usuario no ha subido ningún recibo aún.'
              })">Ver Recibo</button>`
          }
        <br>
          ${
            user.recibo && user.recibo_aprobado == 1
            ? '<em>✔ Aprobado</em>'
            : user.recibo
            ? `<button class="btnAceptar" onclick="aprobarRecibo(${user.id})">Aprobar Recibo</button>`
            : ''
          }
        </td>
      `;
      tbody.appendChild(tr);
    });
    container.appendChild(table);
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
      Swal.fire({
        icon: 'success',
        title: 'Aprobado',
        text: 'El usuario fue aprobado'
      }).then(() => {
        location.reload();
      })
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

function aprobarRecibo(id){
  fetch('public/endpointCooperativas.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
          id: id,
          aprobarRecibo: 1
      })
  })
  .then(res => res.json())
  .then(resp => {
      if(resp.success){
          Swal.fire({
              icon: 'success',
              title: 'Recibo aprobado',
              text: 'El recibo del usuario fue aprobado correctamente'
          }).then(() => location.reload());
      } else {
          Swal.fire({
              icon: 'error',
              title: 'Error',
              text: resp.error || 'No se pudo aprobar el recibo'
          });
      }
  })
  .catch(err => console.error(err));
}
</script>

</body>
</html>
