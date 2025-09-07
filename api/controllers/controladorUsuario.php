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

public function handleUserRequest($method, $data, $files = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        return;
    }

    // =========================
    // REGISTRO
    // =========================
    if (!empty($data['name']) && !empty($data['email']) && !empty($data['contrasena'])) {
        $existingUser = $this->model->getByName($data['name']);
        if ($existingUser) {
            http_response_code(400);
            echo json_encode(['error' => 'Usuario ya existe']);
            return;
        }

        $existingEmail = $this->model->getByEmail($data['email']);
        if ($existingEmail) {
            http_response_code(400);
            echo json_encode(['error' => 'Email ya registrado']);
            return;
        }

        $hashedPass = md5($data['contrasena']);
        $result = $this->model->create($data['name'], $data['email'], $hashedPass, 'no_aprobado', 0);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear usuario']);
        }
        return;
    }

    // =========================
    // LOGIN
    // =========================
    if (isset($data['name']) && isset($data['contrasena'])) {
        $user = $this->model->getByName($data['name']);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            return;
        }

        if ($user['estado'] !== 'aprobado') {
            http_response_code(403);
            echo json_encode(['error' => 'Usuario no aprobado']);
            return;
        }

        $inputPassword = trim($data['contrasena']); 
        $dbPassword = $user['contrasena'];

        $matchPlano = ($inputPassword === $dbPassword);
        $matchHash  = (md5($inputPassword) === $dbPassword);

        if (!$matchPlano && !$matchHash) {
            http_response_code(401);
            echo json_encode(['error' => 'Contraseña incorrecta']);
            return;
        }

        $_SESSION['user'] = $user['nombre'];
        echo json_encode([
            'success' => true,
            'autenticado' => true,
            'redirect' => 'inicio.php'
        ]);
        return;
    }

    // =========================
    // AGREGAR HORAS
    // =========================
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

        $horas = intval($data['horas']);
        if ($horas <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Cantidad de horas inválida']);
            return;
        }

        $success = $this->model->updateHours($user['id'], $horas);
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Horas agregadas correctamente',
                'horas' => $this->model->getHoras($user['id'])
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al agregar horas']);
        }
        return;
    }

    // =========================
    // SUBIR RECIBO
    // =========================
        if (!empty($_FILES['comprobante'])) {
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

        $uploadDir = __DIR__ . '/../../public/uploads/recibos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        // Definir extensiones permitidas
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        $allowedMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png'
        ];

        $fileName = $_FILES['comprobante']['name'];
        $fileTmp  = $_FILES['comprobante']['tmp_name'];
        $fileType = mime_content_type($fileTmp);
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validar extensión
        if (!in_array($fileExt, $allowedExtensions)) {
            http_response_code(400);
            echo json_encode(['error' => 'Extensión de archivo no permitida']);
            return;
        }

        // Validar MIME type
        if (!in_array($fileType, $allowedMimeTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Tipo de archivo no permitido']);
            return;
        }

        $filename = $user['id'] . '_' . time() . '_' . basename($_FILES['comprobante']['name']);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $targetFile)) {
            $success = $this->model->updateRecibo($user['id'], $filename);
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recibo subido correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al registrar el recibo en la base de datos']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al subir el archivo']);
        }
        return;
    }

 // =========================
// OBTENER ESTADÍSTICAS DEL USUARIO
// =========================
if (isset($data['action']) && $data['action'] === 'getStats') {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    $user = $this->model->getByName($_SESSION['user']);
    if (!$user) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }

    $horas = isset($user['horas']) ? $user['horas'] : 0;
    $email = isset($user['email']) ? $user['email'] : '';

    echo json_encode([
        'horas'   => $horas,
        'email'   => $email,
    ]);
    exit;
}

    // =========================
    // SI NO HAY NADA
    // =========================
    http_response_code(400);
    echo json_encode([
        'error' => 'Datos insuficientes',
    ]);
    }
}