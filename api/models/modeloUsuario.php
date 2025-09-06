<?php
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

    public function aprobar($id, $nuevoEstado){
    $stmt = $this->db->prepare("UPDATE users SET estado = ? WHERE id = ?");
    $stmt->bind_param('si', $nuevoEstado, $id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
    }

    public function create($nombre, $email, $contrasena, $estado) {
        $stmt = $this->db->prepare("INSERT INTO users (nombre, email, contrasena, estado) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $nombre, $email, $contrasena, $estado);
        return $stmt->execute();
    }

    public function updateHours($id, $horas){
    $stmt = $this->db->prepare("UPDATE users SET horas = horas + ? WHERE id = ?");
    $stmt->bind_param("ii", $horas, $id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
    }

    public function getHoras($id){
    $stmt = $this->db->prepare("SELECT horas FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['horas'];
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
