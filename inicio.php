<?php
/*
================================================================================
* Project:       https://github.com/domisoft-dev/coviamu
* File:          inicio.php
* Author:        domisoft-dev
* Description:   Página principal para usuarios autenticados.
*                Permite registrar horas trabajadas y subir comprobantes.
================================================================================
* Librerías:
* - Boxicons (https://cdn.boxicons.com)
* - SweetAlert2 (https://cdn.jsdelivr.net/npm/sweetalert2@11)
================================================================================
* Sections Overview:
* - PHP: manejo de sesión y redirección si no hay usuario logueado
* - HTML: estructura de la página, formularios y botones
* - JS: funciones addHours, uploadReceipt, fetchUserStats y eventos submit
================================================================================
*/

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"
    />
    <title>
      COVIAMU - Cooperativa de Viviendas
    </title>
    <link rel="stylesheet" href="public/css/main.css" />
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    </script>
  </head>
  
  <body>
    <form action="api/routes/logout.php" method="post">
      <div class='button-left-container'>
        <button type="submit" class="logout-btn">
          <i class='bx bx-arrow-from-left-stroke'>
          </i>
          Cerrar sesión
        </button>
      </div>
    </form>
    <section id="regist-horas" class="panel-content">
      <div>
        <h1>
          Bienvenid@,
          <?php echo htmlspecialchars($_SESSION[ 'user']); ?>
        </h1>
        <div class="users-stats" id="user-stats">
        </div>
        <div class="form-container">
          <!-- Formulario de horas -->
          <div class="form-box">
            <h2>
              Registrar horas
            </h2>
            <p>
              Ingresa la cantidad de horas trabajadas. Solo se aceptan números positivos.
            </p>
            <form id="horas-form">
              <input type="text" name="horas" id="horas-input" placeholder="Cantidad de horas"
              required>
              <button type="submit" class="bold">
                Aceptar
              </button>
            </form>
          </div>
          <!-- Formulario de recibos -->
          <div class="form-box">
            <h2>
              Enviar comprobantes
            </h2>
            <p class="allowed-files">
              Extensiones permitidas:
              <strong>
                .pdf
              </strong>
              o imágenes (
              <span title=".jpg, .jpeg, .png">
                .jpg, .jpeg, .png
              </span>
              )
            </p>
            <form id="comprobante-form" enctype="multipart/form-data">
              <label for="comprobante">
                Selecciona un archivo:
              </label>
              <input type="file" name="comprobante" id="comprobante" required>
              <button type="submit">
                Subir
              </button>
            </form>
          </div>
        </div>
    </section>
<script>
  function addHours(horas) {
    fetch('public/endpointUsers.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ horas: horas })
    })
    .then(res => res.json())
    .then(data => {
      if(data.success){
        Swal.fire({
          icon: 'success',
          title: 'Horas registradas',
          text: `${data.message}. Total horas: ${data.horas}`
        });
        document.getElementById("horas-input").value = '';
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.error
        });
      }
    })
    .catch(err => console.error("Error en addHours:", err));
  }

  function uploadReceipt(file) {
  const formData = new FormData();
  formData.append('comprobante', file);

  fetch('public/endpointUsers.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if(data.success){
      Swal.fire({
        icon: 'success',
        title: 'Comprobante subido',
        text: data.message
      });
      document.getElementById("comprobante").value = '';
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: data.error || 'No se pudo subir el archivo'
      });
    }
  })
  .catch(err => console.error("Error en uploadReceipt:", err));
}

function fetchUserStats() {
    fetch('public/endpointUsers.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'getStats' })
    })
    .then(res => {
        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Respuesta no es JSON');
        }
        return res.json();
    })
    .then(data => {
        if (data.error) {
            console.error(data.error);
        } else {
            document.getElementById('user-stats').innerHTML = `
                <p>Email: ${data.email}</p>
                <p>Horas trabajadas: ${data.horas}</p>
            `;
        }
    })
    .catch(err => console.error("Error en fetchUserStats:", err));
}

// Llamar al cargar la página
fetchUserStats();

  document.getElementById('horas-form').addEventListener('submit', function(e){
    e.preventDefault();
    const horas = document.getElementById('horas-input').value;
    if(horas) addHours(horas);
  });

  document.getElementById('comprobante-form').addEventListener('submit', function(e){
    e.preventDefault();
    const file = document.getElementById('comprobante').files[0];
    if(file) uploadReceipt(file);
  });
</script>
</body>
</html>
