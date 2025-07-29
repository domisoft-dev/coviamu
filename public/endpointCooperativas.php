<?php
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
