<?php
session_start();
session_unset();
session_destroy();
header("Location: /coviamu/index.html");
exit;
?>

// ruta absoluta /index.html sirve tambien
// depende version del xampp o donde se este ejecutando la web
