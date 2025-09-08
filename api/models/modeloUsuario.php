<?php
/*
================================================================================
* Project:       https://github.com/domisoft-dev/coviamu
* File:          modelousuario.php
* Author:        domisoft-dev
* Description:   Modelo de datos para la gestión de usuarios y administradores.
*                Incluye métodos para CRUD, actualización de horas y recibos,
*                así como manejo de admins y contraseñas.
================================================================================
* Classes:
* - modeloUsuario
*   - __construct(): inicializa la conexión a la base de datos.
*   - getAll(): obtiene todos los usuarios.
*   - getById($id): obtiene un usuario por su ID.
*   - getByName($name): obtiene un usuario por nombre.
*   - getByEmail($email): obtiene un usuario por email.
*   - aprobar($id, $nuevoEstado): actualiza el estado de un usuario.
*   - create($nombre, $email, $contrasena, $estado, $horas): crea un nuevo usuario.
*   - updateHours($id, $horas): suma horas al usuario.
*   - update($id, $nombre, $email, $contrasena, $estado): actualiza datos de usuario.
*   - eliminar($id): elimina un usuario.
*   - updateRecibo($userId, $filename): registra el recibo de un usuario.
*   - getRecibos(): obtiene recibos subidos por los usuarios.
*   - getHoras($id): obtiene las horas de un usuario.
*   - getEmail($id): obtiene el email de un usuario.
*   - getAllAdmins(): obtiene todos los administradores.
*   - getAdminByName($name): obtiene un admin por nombre.
*   - updatePassword($id, $nuevaContrasena): actualiza la contraseña de un admin.
================================================================================
* Libraries: None
================================================================================
*/

require_once __DIR__ . '/../core/database.php';

class modeloUsuario {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM users");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByName($name) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE nombre = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
    }

    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // APROBAR CUENTA
    public function aprobar($id, $nuevoEstado){
    $stmt = $this->db->prepare("UPDATE users SET estado = ? WHERE id = ?");
    $stmt->bind_param('si', $nuevoEstado, $id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
    }

    // APROBAR RECIBO DE USUARIO
    public function aprobarRecibo($userId, $aprobar) {
    $stmt = $this->db->prepare("UPDATE users SET recibo_aprobado = ? WHERE id = ?");
    $stmt->bind_param("ii", $aprobar, $userId);
    $stmt->execute();
    return $stmt->affected_rows > 0;
    }

    public function create($nombre, $email, $contrasena, $estado, $horas) {
        $stmt = $this->db->prepare("INSERT INTO users (nombre, email, contrasena, estado, horas) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssi', $nombre, $email, $contrasena, $estado, $horas);
        return $stmt->execute();
    }

    public function updateHours($id, $horas){
    $stmt = $this->db->prepare("UPDATE users SET horas = horas + ? WHERE id = ?");
    $stmt->bind_param("ii", $horas, $id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
    }

    public function update($id, $nombre, $email, $contrasena, $estado) {
        $stmt = $this->db->prepare("UPDATE users SET nombre=?, email=?, contrasena=?, estado=? WHERE id=?");
        $stmt->bind_param('ssssi', $nombre, $email, $contrasena, $estado, $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function updateRecibo($userId, $filename, $aprobar = 0) {
    $stmt = $this->db->prepare("UPDATE users SET recibo = ?, recibo_aprobado = ? WHERE id = ?");
    $stmt->bind_param("sii", $filename, $aprobar, $userId);
    $stmt->execute();
    return $stmt->affected_rows > 0;
    }

    public function getRecibos() {
    $result = $this->db->query("SELECT id, nombre, recibo FROM users WHERE recibo IS NOT NULL");
    return $result->fetch_all(MYSQLI_ASSOC);
    }

        // GETS
    public function getHoras($id){
        $stmt = $this->db->prepare("SELECT horas FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['horas'];
    }

    public function getEmail($id){
        $stmt = $this->db->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['email'];
    }


        // ADMINS //
    public function getAllAdmins() {
        $result = $this->db->query("SELECT * FROM admins");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAdminByName($name) {
    $stmt = $this->db->prepare("SELECT * FROM admins WHERE nombre = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
    }

    public function updatePassword($id, $nuevaContrasena) {
    $stmt = $this->db->prepare("UPDATE admins SET contrasena = ? WHERE id = ?");
    $stmt->bind_param('si', $nuevaContrasena, $id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
    }
}
