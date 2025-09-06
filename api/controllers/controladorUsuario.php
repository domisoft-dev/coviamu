<?php
require_once __DIR__ . '/../models/modeloUsuario.php';

$response = [];

class controladorUsuario {
    private $model;

    public function __construct() {
        $this->model = new modeloUsuario();
    }

public function agregarHoras($user, $horas) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $user = $this->model->getByName($_SESSION['user']);

    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        return;
    }

    $horas = intval($horas);
    if ($horas <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Cantidad de horas inválida']);
        return;
    }

    $success = $this->model->updateHours($user, $horas);
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Horas agregadas correctamente',
            'horas' => $this->model->getHoras($user)
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al agregar horas']);
    }
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
        if (session_status() === PHP_SESSION_NONE) {
        session_start();
        }
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        if (isset($data['horas'])) {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user = $this->model->getByName($_SESSION['user']);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            return;
        }

        $success = $this->model->updateHours($user['id'], intval($data['horas']));
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Horas agregadas correctamente',
                'horas'   => $this->model->getHoras($user['id'])
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al agregar horas']);
        }
        return;
    }

        if (isset($data['name']) && isset($data['contrasena'])) {
            $user = $this->model->getByName($data['name']);
            if ($user && ($data['contrasena'] === $user['contrasena'] || md5($data['contrasena']) === $user['contrasena'])) {
                if ($user['estado'] === 'aprobado') {
                $_SESSION['user'] = $user['nombre'];
                echo json_encode([
                    'success' => true,
                    'autenticado' => true,
                    'redirect' => 'inicio.php'
            ]);
            return;
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'Usuario no aprobado']);
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
}
}