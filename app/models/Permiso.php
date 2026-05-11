<?php
class Permiso {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getCountActive() {
        $this->db->query("SELECT COUNT(*) as total FROM permisos WHERE estado = 'Pendiente'");
        $row = $this->db->single();
        return $row['total'];
    }

    public function hasPending($alumno_id) {
        $this->db->query("SELECT id FROM permisos WHERE alumno_id = :aid AND estado = 'Pendiente'");
        $this->db->bind(':aid', $alumno_id);
        return $this->db->single();
    }

    public function registrarSalida($alumno_id, $profesor_id, $motivo, $estimatedTime) {
        $this->db->query("INSERT INTO permisos (alumno_id, profesor_id, motivo, hora_estimada_retorno) VALUES (:aid, :pid, :motivo, :estim)");
        $this->db->bind(':aid', $alumno_id);
        $this->db->bind(':pid', $profesor_id);
        $this->db->bind(':motivo', $motivo);
        $this->db->bind(':estim', $estimatedTime);
        return $this->db->execute();
    }

    public function registrarRetorno($permiso_id) {
        $this->db->query("UPDATE permisos SET estado = 'Retornado', hora_retorno = CURRENT_TIME WHERE id = :pid");
        $this->db->bind(':pid', $permiso_id);
        return $this->db->execute();
    }

    public function getLogsByDate($fecha) {
        $this->db->query("
            SELECT p.*, a.nombres, a.apellidos, gs.grado, gs.seccion, u.nombre as profesor
            FROM permisos p
            JOIN alumnos a ON p.alumno_id = a.id
            JOIN grado_secciones gs ON a.grado_seccion_id = gs.id
            JOIN usuarios u ON p.profesor_id = u.id
            WHERE p.fecha = :fecha
            ORDER BY p.hora_salida DESC
        ");
        $this->db->bind(':fecha', $fecha);
        return $this->db->resultSet();
    }
}
