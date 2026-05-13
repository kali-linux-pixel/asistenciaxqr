<?php
class Alerta {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Contar alertas no leídas para el usuario actual
    public function countUnread($usuario_id, $rol) {
        $this->db->query("SELECT COUNT(*) as total FROM alertas 
            WHERE leida = 0 
            AND (para_usuario_id = :uid OR para_usuario_id IS NULL)
            AND (para_rol = :rol OR para_rol = 'ambos')");
        $this->db->bind(':uid', $usuario_id);
        $this->db->bind(':rol', $rol);
        $row = $this->db->single();
        return $row['total'] ?? 0;
    }

    // Obtener alertas recientes
    public function getRecent($usuario_id, $rol, $limit = 8) {
        $this->db->query("SELECT * FROM alertas 
            WHERE (para_usuario_id = :uid OR para_usuario_id IS NULL)
            AND (para_rol = :rol OR para_rol = 'ambos')
            ORDER BY created_at DESC LIMIT :lim");
        $this->db->bind(':uid', $usuario_id);
        $this->db->bind(':rol', $rol);
        $this->db->bind(':lim', $limit);
        return $this->db->resultSet();
    }

    // Marcar como leída
    public function markRead($id) {
        $this->db->query("UPDATE alertas SET leida = 1 WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Marcar todas como leídas para este usuario
    public function markAllRead($usuario_id, $rol) {
        $this->db->query("UPDATE alertas SET leida = 1 
            WHERE (para_usuario_id = :uid OR para_usuario_id IS NULL)
            AND (para_rol = :rol OR para_rol = 'ambos')");
        $this->db->bind(':uid', $usuario_id);
        $this->db->bind(':rol', $rol);
        return $this->db->execute();
    }

    // Crear alerta manual
    public function crear($tipo, $titulo, $mensaje, $para_rol = 'director', $link = null, $alumno_id = null, $para_usuario_id = null) {
        $this->db->query("INSERT INTO alertas (tipo, titulo, mensaje, para_rol, para_usuario_id, alumno_id, link) 
            VALUES (:tipo, :titulo, :mensaje, :rol, :uid, :aid, :link)");
        $this->db->bind(':tipo', $tipo);
        $this->db->bind(':titulo', $titulo);
        $this->db->bind(':mensaje', $mensaje);
        $this->db->bind(':rol', $para_rol);
        $this->db->bind(':uid', $para_usuario_id);
        $this->db->bind(':aid', $alumno_id);
        $this->db->bind(':link', $link);
        return $this->db->execute();
    }

    // Auto-generar alertas de alumnos en riesgo por asistencia
    public function generarAlertasRiesgo() {
        // Detectar alumnos con asistencia < 70% en últimos 30 días
        $this->db->query("
            SELECT a.id, a.nombres, a.apellidos, gs.grado, gs.seccion,
                COUNT(as2.id) as total_dias,
                SUM(CASE WHEN as2.estado = 'presente' THEN 1 ELSE 0 END) as presentes
            FROM alumnos a
            JOIN grado_secciones gs ON a.grado_seccion_id = gs.id
            LEFT JOIN asistencias as2 ON as2.alumno_id = a.id 
                AND as2.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            WHERE a.estado = 1
            GROUP BY a.id
            HAVING total_dias > 5 AND (presentes/total_dias) < 0.70
        ");
        $enRiesgo = $this->db->resultSet();

        foreach ($enRiesgo as $alumno) {
            $pct = $alumno['total_dias'] > 0 ? round(($alumno['presentes']/$alumno['total_dias'])*100) : 0;
            // Solo crear si no existe una similar no leída en últimas 48h
            $this->db->query("SELECT id FROM alertas WHERE alumno_id = :aid AND tipo = 'riesgo_asistencia' 
                AND leida = 0 AND created_at >= DATE_SUB(NOW(), INTERVAL 48 HOUR)");
            $this->db->bind(':aid', $alumno['id']);
            $existe = $this->db->single();
            if (!$existe) {
                $this->crear(
                    'riesgo_asistencia',
                    "⚠️ Alumno en Riesgo de Asistencia",
                    "{$alumno['apellidos']}, {$alumno['nombres']} ({$alumno['grado']} {$alumno['seccion']}) tiene solo {$pct}% de asistencia en los últimos 30 días.",
                    'director',
                    URLROOT . '/alumnos',
                    $alumno['id']
                );
            }
        }
        return count($enRiesgo);
    }

    // Alertas de notas pendientes (profesores sin notas)
    public function generarAlertasNotasPendientes($periodo = '1er Bimestre') {
        $this->db->query("
            SELECT u.id, u.nombre, gs.grado, gs.seccion,
                COUNT(DISTINCT a.id) as total_alumnos,
                COUNT(DISTINCT n.alumno_id) as con_notas
            FROM usuarios u
            JOIN grado_secciones gs ON u.grado_seccion_id = gs.id
            LEFT JOIN alumnos a ON a.grado_seccion_id = gs.id
            LEFT JOIN notas n ON n.alumno_id = a.id AND n.periodo = :periodo
            WHERE u.rol = 'profesor' AND u.grado_seccion_id IS NOT NULL
            GROUP BY u.id
            HAVING total_alumnos > 0 AND con_notas = 0
        ");
        $this->db->bind(':periodo', $periodo);
        $sinNotas = $this->db->resultSet();

        foreach ($sinNotas as $prof) {
            $this->db->query("SELECT id FROM alertas WHERE tipo = 'notas_pendientes' 
                AND para_usuario_id = :uid AND leida = 0 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $this->db->bind(':uid', $prof['id']);
            $existe = $this->db->single();
            if (!$existe) {
                $this->crear(
                    'notas_pendientes',
                    "📝 Calificaciones Pendientes",
                    "Prof. {$prof['nombre']} ({$prof['grado']} {$prof['seccion']}) no ha subido calificaciones del {$periodo}.",
                    'director',
                    URLROOT . '/notas'
                );
            }
        }
        return count($sinNotas);
    }

    // Obtener alumnos en riesgo para widget del dashboard
    public function getAlumnosEnRiesgo($limit = 5) {
        $this->db->query("
            SELECT a.id, a.nombres, a.apellidos, gs.grado, gs.seccion,
                COUNT(as2.id) as total_dias,
                SUM(CASE WHEN as2.estado = 'presente' THEN 1 ELSE 0 END) as presentes,
                ROUND(SUM(CASE WHEN as2.estado = 'presente' THEN 1 ELSE 0 END) / COUNT(as2.id) * 100) as pct_asistencia
            FROM alumnos a
            JOIN grado_secciones gs ON a.grado_seccion_id = gs.id
            LEFT JOIN asistencias as2 ON as2.alumno_id = a.id 
                AND as2.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            WHERE a.estado = 1
            GROUP BY a.id
            HAVING total_dias > 3 AND pct_asistencia < 75
            ORDER BY pct_asistencia ASC
            LIMIT :lim
        ");
        $this->db->bind(':lim', $limit);
        return $this->db->resultSet();
    }
}
