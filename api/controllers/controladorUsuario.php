<?php
require_once __DIR__ . '/../models/modeloUsuario.php';

$response = [];

class controladorUsuario {
    private $model;

    public function __construct() {
        $this->model = new modeloUsuario();
    }



public function verificarAdmin($nombre, $contrasena) {
    $user = $this->model->getAdminByName($nombre);

    if (!$user) {
        return false;
    }

    $passDB = $user['contrasena'];
    $inputPass = $contrasena;

    if (preg_match('/^[a-f0-9]{32}$/i', $passDB)) {
        if (md5($inputPass) !== $passDB) {
            return false;
        }
    } else {
        if ($inputPass !== $passDB) {
            return false;
        }
        $hashed = md5($inputPass);
        $this->model->updatePassword($user['id'], $hashed);
    }

    return true;
}

public function handleAdminRequest($method, $data) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['admin'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    if ($method === 'POST' && isset($data['id']) && isset($data['estado'])) {
        $success = $this->model->aprobar($data['id'], $data['estado']);
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Error al actualizar estado']);
        }
        exit;
    }

    if ($method === 'POST' && isset($data['id']) && !isset($data['estado'])) {
        $success = $this->model->eliminar($data['id']);
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Error al eliminar usuario']);
        }
        exit;
    }

    $users = $this->model->getAll();
    echo json_encode($users);
}

    public function handleUserRequest($method, $data) {
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (!empty($data['name']) && !empty($data['contrasena']) && empty($data['email'])) {
            $response = ['check' => 'ok'];
            $user = $this->model->getByName($data['name']);
        if ($user && md5($data['contrasena']) === $user['contrasena']) {
            $response['check2'] = 'ok';
        if ($user['estado'] === 'aprobado') {
            $response['check3'] = 'ok';
            $_SESSION['user'] = $user['nombre'];
            $response['success'] = true;
            $response['redirect'] = 'inicio.php';
        } else {
            http_response_code(403);
            $response = ['error' => 'Usuario no aprobado'];
        }
        } else {
        http_response_code(401);

        $response = ['error' => 'Credenciales inválidas'];
    }
    echo json_encode($response);
    return;
}

        if (!empty($data['name']) && !empty($data['email']) && !empty($data['contrasena'])) {
            $hashedPass = md5($data['contrasena']);
            $result = $this->model->create(
                $data['name'],
                $data['email'],
                $hashedPass,
                'no_aprobado'
            );
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear usuario']);
            }
            return;
        }

        http_response_code(400);
        echo json_encode(['error' => 'Datos insuficientes']);
    }
}
