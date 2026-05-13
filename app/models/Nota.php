<?php
class Nota {
    private $db;
    public function __construct() { $this->db = new Database; }

    public function getNotasByAlumno($alumno_id) {
        $this->db->query("
            SELECT n.*, c.nombre as curso_nombre 
            FROM notas n
            JOIN cursos c ON n.curso_id = c.id
            WHERE n.alumno_id = :aid
            ORDER BY c.nombre ASC, n.periodo ASC
        ");
        $this->db->bind(':aid', $alumno_id);
        return $this->db->resultSet();
    }

    public function getNotasByCursoYGrado($curso_id, $grado_seccion_id) {
        // Obtener lista de alumnos del grado y cruzar con la tabla notas de ese curso
        $this->db->query("
            SELECT a.id as alumno_id, a.nombres, a.apellidos, n.nota, n.periodo, n.comentario, n.id as nota_id
            FROM alumnos a
            LEFT JOIN notas n ON a.id = n.alumno_id AND n.curso_id = :cid
            WHERE a.grado_seccion_id = :gsid
            ORDER BY a.apellidos, a.nombres
        ");
        $this->db->bind(':cid', $curso_id);
        $this->db->bind(':gsid', $grado_seccion_id);
        return $this->db->resultSet();
    }

    public function registrarNota($data) {
        // Verificar si ya existe para este alumno, curso y periodo para ACTUALIZAR en vez de DUPLICAR
        $this->db->query("SELECT id FROM notas WHERE alumno_id = :aid AND curso_id = :cid AND periodo = :periodo");
        $this->db->bind(':aid', $data['alumno_id']);
        $this->db->bind(':cid', $data['curso_id']);
        $this->db->bind(':periodo', $data['periodo']);
        $row = $this->db->single();

        if ($row) {
            // Actualizar
            $this->db->query("UPDATE notas SET nota = :nota, comentario = :cmt, fecha_registro = CURRENT_DATE WHERE id = :nid");
            $this->db->bind(':nota', $data['nota']);
            $this->db->bind(':cmt', $data['comentario']);
            $this->db->bind(':nid', $row['id']);
        } else {
            // Insertar
            $this->db->query("INSERT INTO notas (alumno_id, curso_id, periodo, nota, comentario, fecha_registro) VALUES (:aid, :cid, :periodo, :nota, :cmt, CURRENT_DATE)");
            $this->db->bind(':aid', $data['alumno_id']);
            $this->db->bind(':cid', $data['curso_id']);
            $this->db->bind(':periodo', $data['periodo']);
            $this->db->bind(':nota', $data['nota']);
            $this->db->bind(':cmt', $data['comentario']);
        }
        return $this->db->execute();
    }

    public function getComplianceReport($periodo) {
        // Listar todas las aulas, sus profesores asignados y conteo de notas existentes
        $this->db->query("
            SELECT 
                gs.id as grado_id,
                CONCAT(gs.grado, ' ', gs.seccion) as aula,
                u.nombre as profesor_nombre,
                (SELECT COUNT(*) FROM alumnos WHERE grado_seccion_id = gs.id) as total_alumnos,
                (SELECT COUNT(*) FROM notas n 
                 JOIN alumnos al ON n.alumno_id = al.id 
                 WHERE al.grado_seccion_id = gs.id AND n.periodo = :periodo) as total_notas
            FROM grado_secciones gs
            LEFT JOIN usuarios u ON u.grado_seccion_id = gs.id AND u.rol = 'profesor'
            ORDER BY gs.grado, gs.seccion
        ");
        $this->db->bind(':periodo', $periodo);
        return $this->db->resultSet();
    }
}
