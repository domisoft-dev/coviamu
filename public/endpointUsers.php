<?php
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
