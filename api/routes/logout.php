<?php
/*
================================================================================
* Project:       https://github.com/domisoft-dev/coviamu
* File:          logout.php
* Author:        domisoft-dev
* Description:   Cierra la sesión del usuario o administrador, liberando todas
*                las variables de sesión y redirigiendo a la página de inicio.
================================================================================
* Functionality:
* - session_start(): inicia la sesión actual.
* - session_unset(): elimina todas las variables de sesión.
* - session_destroy(): destruye la sesión.
* - header("Location: /coviamu/index.html"): redirige a la página principal.
================================================================================
* Notes:
* - La ruta absoluta /index.html funciona dependiendo de la versión de XAMPP
*   o del entorno donde se ejecute la web.
================================================================================
* Libraries: None
================================================================================
*/

session_start();
session_unset();
session_destroy();
header("Location: /coviamu/index.html");
exit;
?>
