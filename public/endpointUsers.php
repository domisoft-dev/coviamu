<?php
session_start();
require_once '../api/controllers/controladorUsuario.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Solo método POST permitido']);
    exit;
}

$controller = new controladorUsuario();
$controller->handleUserRequest($method, $data);