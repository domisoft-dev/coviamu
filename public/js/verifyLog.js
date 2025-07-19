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