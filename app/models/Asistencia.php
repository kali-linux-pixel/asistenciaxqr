<?php
class Asistencia {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getCountToday() {
        $this->db->query("SELECT COUNT(*) as total FROM asistencias WHERE fecha = CURRENT_DATE");
        $row = $this->db->single();
        return $row['total'];
    }

    public function getWeeklyStats() {
        $threshold = date('Y-m-d', strtotime('-6 days'));
        $this->db->query("
            SELECT fecha, COUNT(*) as total 
            FROM asistencias 
            WHERE fecha >= :thresh
            GROUP BY fecha
            ORDER BY fecha ASC
        ");
        $this->db->bind(':thresh', $threshold);
        return $this->db->resultSet();
    }

    public function getAttendanceByGrado() {
        $this->db->query("
            SELECT CONCAT(gs.grado, ' ', gs.seccion) as label, COUNT(a.id) as total
            FROM grado_secciones gs
            LEFT JOIN alumnos al ON al.grado_seccion_id = gs.id
            LEFT JOIN asistencias a ON a.alumno_id = al.id AND a.fecha = CURRENT_DATE
            GROUP BY gs.id
            ORDER BY gs.grado, gs.seccion
        ");
        return $this->db->resultSet();
    }

    public function getRecientes() {
        $this->db->query("
            SELECT a.nombres, a.apellidos, 'Asistencia' as tipo, ast.hora_entrada as hora, ast.fecha
            FROM asistencias ast
            JOIN alumnos a ON ast.alumno_id = a.id
            WHERE ast.fecha = CURRENT_DATE
            UNION ALL
            SELECT a.nombres, a.apellidos, CONCAT('Permiso: ', p.motivo) as tipo, p.hora_salida as hora, p.fecha
            FROM permisos p
            JOIN alumnos a ON p.alumno_id = a.id
            WHERE p.fecha = CURRENT_DATE
            ORDER BY hora DESC
            LIMIT 5
        ");
        return $this->db->resultSet();
    }

    public function checkTodayAttendance($alumno_id) {
        $this->db->query("SELECT * FROM asistencias WHERE alumno_id = :aid AND fecha = CURRENT_DATE");
        $this->db->bind(':aid', $alumno_id);
        return $this->db->single();
    }

    public function registrarEntrada($alumno_id) {
        $this->db->query("INSERT INTO asistencias (alumno_id) VALUES (:aid)");
        $this->db->bind(':aid', $alumno_id);
        return $this->db->execute();
    }

    public function registrarSalida($registro_id) {
        $this->db->query("UPDATE asistencias SET hora_salida = CURRENT_TIME WHERE id = :rid");
        $this->db->bind(':rid', $registro_id);
        return $this->db->execute();
    }

    public function getLogsByDate($fecha, $grado_id = '') {
        $sql = "
            SELECT ast.*, a.nombres, a.apellidos, a.dni, gs.grado, gs.seccion
            FROM asistencias ast
            JOIN alumnos a ON ast.alumno_id = a.id
            JOIN grado_secciones gs ON a.grado_seccion_id = gs.id
            WHERE ast.fecha = :fecha
        ";
        
        if (!empty($grado_id)) {
            $sql .= " AND gs.id = :gsid";
        }
        
        $sql .= " ORDER BY ast.hora_entrada DESC";
        
        $this->db->query($sql);
        $this->db->bind(':fecha', $fecha);
        
        if (!empty($grado_id)) {
            $this->db->bind(':gsid', $grado_id);
        }
        
        return $this->db->resultSet();
    }
}
