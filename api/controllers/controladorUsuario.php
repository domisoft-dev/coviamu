<?php
/*
================================================================================
* Project:       https://github.com/domisoft-dev/coviamu
* File:          controladorUsuario.php
* Author:        domisoft-dev
* Description:   Controlador principal que maneja las operaciones de usuarios 
*                y administradores en el sistema COVIAMU.
================================================================================
* Classes:
* - controladorUsuario
*   - __construct(): inicializa el modelo de usuario.
*   - verificarAdmin($nombre, $contrasena): verifica credenciales de administrador.
*   - handleAdminRequest($method, $data): maneja acciones de administrador
*     (aprobar usuarios, eliminar usuarios, obtener listado).
*   - handleUserRequest($method, $data, $files = null): maneja acciones de usuarios
*     (registro, login, agregar horas, subir recibo, obtener estadísticas).
================================================================================
* Notes:
* - Se utilizan sesiones para validar usuarios y administradores.
* - Las contraseñas se almacenan en MD5; si se encuentra en texto plano, se convierte automáticamente.
* - Se manejan respuestas JSON y códigos HTTP adecuados según la operación.
* - Validaciones de archivos subidos incluyen extensión y MIME type permitidos.
================================================================================
* Libraries: None (requiere modeloUsuario.php)
================================================================================
*/

<?php
require_once __DIR__ . '/../models/modeloUsuario.php';

$response = [];

class controladorUsuario {
    private $model;

    /*
    ================================================================================
    * Block: Constructor
    * Description: Inicializa el modelo de usuario.
    ================================================================================
    */
    public function __construct() {
        $this->model = new modeloUsuario();
    }

    /*
    ================================================================================
    * Block: verificarAdmin
    * Description: Verifica credenciales de administrador, actualiza MD5 si es necesario.
    ================================================================================
    */
    public function verificarAdmin($nombre, $contrasena) {
        $user = $this->model->getAdminByName($nombre);
        if (!$user) return false;

        $passDB = $user['contrasena'];
        $inputPass = $contrasena;

        if (preg_match('/^[a-f0-9]{32}$/i', $passDB)) {
            if (md5($inputPass) !== $passDB) return false;
        } else {
            if ($inputPass !== $passDB) return false;
            $hashed = md5($inputPass);
            $this->model->updatePassword($user['id'], $hashed);
        }
        return true;
    }

    /*
    ================================================================================
    * Block: handleAdminRequest
    * Description: Maneja solicitudes de administrador (aprobar/eliminar usuarios, listar).
    ================================================================================
    */
    public function handleAdminRequest($method, $data) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['admin'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        if ($method === 'POST' && isset($data['id']) && isset($data['estado'])) {
            $success = $this->model->aprobar($data['id'], $data['estado']);
            echo json_encode($success ? ['success'=>true] : ['success'=>false,'error'=>'Error al actualizar estado']);
            exit;
        }

        if ($method === 'POST' && isset($data['id']) && !isset($data['estado'])) {
            $success = $this->model->eliminar($data['id']);
            echo json_encode($success ? ['success'=>true] : ['success'=>false,'error'=>'Error al eliminar usuario']);
            exit;
        }

        $users = $this->model->getAll();
        echo json_encode($users);
    }

    /*
    ================================================================================
    * Block: handleUserRequest
    * Description: Maneja acciones de usuario (registro, login, agregar horas, subir recibo, obtener estadísticas).
    ================================================================================
    */
    public function handleUserRequest($method, $data, $files = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        /*
        ================================================================================
        * Block: Registro
        * Description: Registra un nuevo usuario y valida nombre/email.
        ================================================================================
        */
        if (!empty($data['name']) && !empty($data['email']) && !empty($data['contrasena'])) {
            $existingUser = $this->model->getByName($data['name']);
            if ($existingUser) { http_response_code(400); echo json_encode(['error'=>'Usuario ya existe']); return; }

            $existingEmail = $this->model->getByEmail($data['email']);
            if ($existingEmail) { http_response_code(400); echo json_encode(['error'=>'Email ya registrado']); return; }

            $hashedPass = md5($data['contrasena']);
            $result = $this->model->create($data['name'], $data['email'], $hashedPass, 'no_aprobado', 0);
            echo json_encode($result ? ['success'=>true] : ['error'=>'Error al crear usuario']);
            return;
        }

        /*
        ================================================================================
        * Block: Login
        * Description: Autentica usuario, verifica estado aprobado y contraseña.
        ================================================================================
        */
        if (isset($data['name']) && isset($data['contrasena'])) {
            $user = $this->model->getByName($data['name']);
            if (!$user) { http_response_code(404); echo json_encode(['error'=>'Usuario no encontrado']); return; }
            if ($user['estado'] !== 'aprobado') { http_response_code(403); echo json_encode(['error'=>'Usuario no aprobado']); return; }

            $inputPassword = trim($data['contrasena']); 
            $dbPassword = $user['contrasena'];
            $matchPlano = ($inputPassword === $dbPassword);
            $matchHash  = (md5($inputPassword) === $dbPassword);

            if (!$matchPlano && !$matchHash) { http_response_code(401); echo json_encode(['error'=>'Contraseña incorrecta']); return; }

            $_SESSION['user'] = $user['nombre'];
            echo json_encode(['success'=>true,'autenticado'=>true,'redirect'=>'inicio.php']);
            return;
        }

        /*
        ================================================================================
        * Block: Agregar horas
        * Description: Suma horas al usuario logueado y valida cantidad.
        ================================================================================
        */
        if (isset($data['horas'])) {
            if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'No autorizado']); return; }
            $user = $this->model->getByName($_SESSION['user']);
            if (!$user) { http_response_code(404); echo json_encode(['error'=>'Usuario no encontrado']); return; }

            $horas = intval($data['horas']);
            if ($horas <= 0) { http_response_code(400); echo json_encode(['error'=>'Cantidad de horas inválida']); return; }

            $success = $this->model->updateHours($user['id'], $horas);
            echo json_encode($success ? ['success'=>true,'message'=>'Horas agregadas correctamente','horas'=>$this->model->getHoras($user['id'])] : ['error'=>'Error al agregar horas']);
            return;
        }

        /*
        ================================================================================
        * Block: Subir recibo
        * Description: Valida y guarda el recibo del usuario logueado.
        ================================================================================
        */
        if (!empty($_FILES['comprobante'])) {
            if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'No autorizado']); return; }
            $user = $this->model->getByName($_SESSION['user']);
            if (!$user) { http_response_code(404); echo json_encode(['error'=>'Usuario no encontrado']); return; }

            $uploadDir = __DIR__ . '/../../public/uploads/recibos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $allowedExtensions = ['pdf','jpg','jpeg','png'];
            $allowedMimeTypes = ['application/pdf','image/jpeg','image/png'];

            $fileName = $_FILES['comprobante']['name'];
            $fileTmp  = $_FILES['comprobante']['tmp_name'];
            $fileType = mime_content_type($fileTmp);
            $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedExtensions) || !in_array($fileType, $allowedMimeTypes)) {
                http_response_code(400); echo json_encode(['error'=>'Archivo no permitido']); return;
            }

            $filename = $user['id'].'_'.time().'_'.basename($fileName);
            $targetFile = $uploadDir.$filename;

            if (move_uploaded_file($fileTmp, $targetFile)) {
                $success = $this->model->updateRecibo($user['id'], $filename);
                echo json_encode($success ? ['success'=>true,'message'=>'Recibo subido correctamente'] : ['error'=>'Error al registrar el recibo']);
            } else {
                http_response_code(500); echo json_encode(['error'=>'Error al subir el archivo']);
            }
            return;
        }

        /*
        ================================================================================
        * Block: Obtener estadísticas
        * Description: Devuelve horas y email del usuario logueado.
        ================================================================================
        */
        if (isset($data['action']) && $data['action'] === 'getStats') {
            if (!isset($_SESSION['user'])) { echo json_encode(['error'=>'No autorizado']); exit; }
            $user = $this->model->getByName($_SESSION['user']);
            if (!$user) { echo json_encode(['error'=>'Usuario no encontrado']); exit; }

            $horas = $user['horas'] ?? 0;
            $email = $user['email'] ?? '';

            echo json_encode(['horas'=>$horas,'email'=>$email]);
            exit;
        }

        /*
        ================================================================================
        * Block: Datos insuficientes
        * Description: Se ejecuta cuando no hay datos válidos en la solicitud.
        ================================================================================
        */
        http_response_code(400);
        echo json_encode(['error'=>'Datos insuficientes']);
    }
}