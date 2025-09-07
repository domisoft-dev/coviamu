/*
================================================================================
* Project:       https://github.com/domisoft-dev/coviamu
* File:          verifyLog.js
* Author:        domisoft-dev
* Description:   Script de verificación de sesión para COVIAMU.
*                Redirige automáticamente a la página correspondiente según
*                el tipo de usuario logueado (admin o user).
================================================================================
* Sections Overview:
* - window.addEventListener("DOMContentLoaded", ...): espera a que el DOM esté cargado.
* - fetch("api/routes/session.php", { method: "GET", credentials: "include" }): solicita la sesión activa al servidor.
* - .then(res => res.json()): convierte la respuesta en JSON.
* - if (data.logAdmin): si el admin está logueado, redirige a panel.php.
* - else if (data.logUser): si el usuario está logueado, redirige a inicio.php.
* - else: no hay sesión activa, se muestra mensaje en consola.
* - .catch(err => ...): captura errores en la verificación y los muestra en consola.
================================================================================
*/

window.addEventListener("DOMContentLoaded", () => {
  fetch("api/routes/session.php", {
    method: "GET",
    credentials: "include"
  })
  .then(res => res.json())
  .then(data => {
    if (data.logAdmin) {
      window.location.href = "panel.php";
    } else if(data.logUser) {
      window.location.href = "inicio.php";
    } else {
      console.log("No esta logeado.");
    }
  })
  .catch(err => {
    console.error("Error al verificar sesión:", err);
  });
});