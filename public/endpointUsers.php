<?php
/*
================================================================================
* Project:       https://github.com/domisoft-dev/coviamu
* File:          endpointUsers.php
* Author:        domisoft-dev
* Description:   Endpoint para manejar solicitudes de usuarios en el sistema COVIAMU.
*                Recibe peticiones POST, procesa los datos y llama al controlador
*                correspondiente para registrar, actualizar o consultar usuarios.
================================================================================
* Sections Overview:
* - session_start(): inicia la sesión de PHP para manejar datos de usuario.
* - header('Content-Type: application/json'): define la respuesta como JSON.
* - require_once '../api/controllers/controladorUsuario.php': incluye el controlador de usuarios.
* - $method: obtiene el método HTTP de la petición.
* - $data: decodifica los datos JSON enviados en la petición, si no existen usa $_REQUEST.
* - Método permitido: solo POST. Responde con 405 si se usa otro método.
* - $controller->handleUserRequest($method, $data): envía la petición al controlador para procesamiento.
================================================================================
*/

session_start();
header('Content-Type: application/json');
require_once '../api/controllers/controladorUsuario.php';


$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_REQUEST;
}

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Solo método POST permitido']);
    exit;
}

$controller = new controladorUsuario();
$controller->handleUserRequest($method, $data);
