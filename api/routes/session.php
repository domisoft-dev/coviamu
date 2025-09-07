<?php
/*
================================================================================
* Project:       https://github.com/domisoft-dev/coviamu
* File:          session.php
* Author:        domisoft-dev
* Description:   Verifica el estado de sesión del usuario o administrador y
*                devuelve un JSON indicando si está logueado.
================================================================================
* Functionality:
* - session_start(): inicia la sesión actual.
* - Comprueba si $_SESSION['admin'] está definida:
*     -> Devuelve logAdmin = true y el nombre del admin.
* - Comprueba si $_SESSION['user'] está definida:
*     -> Devuelve logUser = true y el nombre del usuario.
* - Si ninguna está definida:
*     -> Devuelve logueado = false.
* - echo json_encode($response): retorna el estado de sesión en formato JSON.
================================================================================
* Libraries: None
================================================================================
*/

session_start();

$response = [];

if (isset($_SESSION['admin'])) {
    $response = [
        "logAdmin" => true,
        "usuario" => $_SESSION['admin']
    ];
} else if(isset($_SESSION['user'])) {
    $response = [
        "logUser" => true,
        "usuario" => $_SESSION['user']
    ];
} else {
    $response = [
        "logueado" => false
    ];
}

echo json_encode($response);
?>