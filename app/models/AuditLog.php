<?php
class AuditLog {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function log($accion, $descripcion = '') {
        $usuario_id   = $_SESSION['user_id'] ?? 0;
        $usuario_nombre = $_SESSION['user_nombre'] ?? 'Sistema';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $this->db->query("INSERT INTO audit_log (usuario_id, usuario_nombre, accion, descripcion, ip) 
            VALUES (:uid, :nombre, :accion, :desc, :ip)");
        $this->db->bind(':uid', $usuario_id);
        $this->db->bind(':nombre', $usuario_nombre);
        $this->db->bind(':accion', $accion);
        $this->db->bind(':desc', $descripcion);
        $this->db->bind(':ip', $ip);
        return $this->db->execute();
    }

    public function getRecent($limit = 50) {
        $this->db->query("SELECT * FROM audit_log ORDER BY created_at DESC LIMIT :lim");
        $this->db->bind(':lim', $limit);
        return $this->db->resultSet();
    }
}
