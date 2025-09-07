<?php
/*
================================================================================
* Project:       https://github.com/domisoft-dev/coviamu
* File:          endpointCooperativas.php
* Author:        domisoft-dev
* Description:   Endpoint para manejar solicitudes de administradores en COVIAMU.
*                Controla la autenticación de admins y permite operaciones de
*                gestión de usuarios registrados en la cooperativa.
================================================================================
* Sections Overview:
* - session_start(): inicia la sesión para manejar datos de administrador.
* - header('Content-Type: application/json'): define la respuesta como JSON.
* - require_once '../api/controllers/controladorUsuario.php': incluye el controlador de usuarios.
* - function verificarAdmin(): valida que el admin esté logueado, si no devuelve error JSON.
* - $method y $data: obtienen método HTTP y datos enviados en la petición.
* - Verificación de login de admin: si se envían 'nombre' y 'contrasena', se valida con el controlador y se crea sesión.
* - Respuesta JSON: success, autenticado y redirect en caso de éxito, o error si falla.
* - verificarAdmin(): asegura que cualquier otra operación requiera sesión de admin.
* - $controller->handleAdminRequest($method, $data): delega el manejo de solicitudes administrativas al controlador.
================================================================================
*/

session_start();
header('Content-Type: application/json');

require_once '../api/controllers/controladorUsuario.php';

function verificarAdmin() {
    if (!isset($_SESSION['admin'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Acceso no autorizado',
        ]);
        exit;
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_REQUEST;
}

$controller = new controladorUsuario();

if (isset($data['nombre']) && isset($data['contrasena'])) {
    if ($controller->verificarAdmin($data['nombre'], $data['contrasena'])) {
        $_SESSION['admin'] = $data['nombre'];
        echo json_encode([
            'success' => true,
            'autenticado' => true,
            'redirect' => 'panel.php'
        ]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas']);
        exit;
    }
}

verificarAdmin();

$controller->handleAdminRequest($method, $data);
