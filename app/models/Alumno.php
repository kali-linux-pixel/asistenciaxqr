<?php
class Alumno {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAlumnos() {
        $this->db->query("SELECT a.*, gs.grado, gs.seccion 
                         FROM alumnos a 
                         JOIN grado_secciones gs ON a.grado_seccion_id = gs.id 
                         ORDER BY a.id DESC");
        return $this->db->resultSet();
    }

    public function getAlumnosByGrado($gradoId) {
        $this->db->query("SELECT a.*, gs.grado, gs.seccion 
                         FROM alumnos a 
                         JOIN grado_secciones gs ON a.grado_seccion_id = gs.id 
                         WHERE a.grado_seccion_id = :grado_id
                         ORDER BY a.apellidos ASC, a.nombres ASC");
        $this->db->bind(':grado_id', $gradoId);
        return $this->db->resultSet();
    }

    public function getGrades() {
        $this->db->query("SELECT * FROM grado_secciones ORDER BY grado, seccion");
        return $this->db->resultSet();
    }

    public function getGradeIdByNames($grado, $seccion) {
        $gradoLimpio = trim($grado);
        $seccLimpia  = trim($seccion);

        // 1. Intentar coincidencia exacta primero (Rápido)
        $this->db->query("SELECT id FROM grado_secciones WHERE LOWER(grado) = LOWER(:grado) AND LOWER(seccion) = LOWER(:seccion) LIMIT 1");
        $this->db->bind(':grado', $gradoLimpio);
        $this->db->bind(':seccion', $seccLimpia);
        $row = $this->db->single();
        if ($row) return $row['id'];

        // 2. Búsqueda Inteligente y Tolerante (Fuzzy Matching)
        // Si el Excel dice "4to", "4", "4°" y en BD dice "4to Grado", los emparejamos por el número central.
        $this->db->query("SELECT id, grado, seccion FROM grado_secciones");
        $gradosDB = $this->db->resultSet();

        // Extraer el primer número que aparezca en la entrada (ej: "4to Grado" -> 4, "3er Año" -> 3)
        preg_match('/\d+/', $gradoLimpio, $matchEntrada);
        $numEntrada = $matchEntrada[0] ?? null;
        $secEntrada = strtoupper($seccLimpia);

        // Limpieza extra de comillas por si acaso en sección
        $secEntrada = str_replace(['"', "'", '“', '”', '«', '»'], '', $secEntrada);

        if ($numEntrada !== null) {
            foreach ($gradosDB as $g) {
                preg_match('/\d+/', $g['grado'], $matchBD);
                $numBD = $matchBD[0] ?? null;
                $secBD = strtoupper(trim($g['seccion']));
                
                // Quitar comillas también de la sección de BD para comparar limpio
                $secBD = str_replace(['"', "'", '“', '”', '«', '»'], '', $secBD);

                if ($numBD === $numEntrada && $secBD === $secEntrada) {
                    return $g['id']; // ¡Encontrado por inteligencia de patrones!
                }
            }
        }

        return null;
    }

    public function registrarAlumno($data) {
        $this->db->query("INSERT INTO alumnos (nombres, apellidos, dni, grado_seccion_id, qr_token) VALUES (:nombres, :apellidos, :dni, :grado_seccion_id, :qr_token)");
        
        // Generate random token
        $token = substr(bin2hex(random_bytes(12)), 0, 12);

        $this->db->bind(':nombres', $data['nombres']);
        $this->db->bind(':apellidos', $data['apellidos']);
        $this->db->bind(':dni', $data['dni']);
        $this->db->bind(':grado_seccion_id', $data['grado_seccion']);
        $this->db->bind(':qr_token', $token);

        return $this->db->execute();
    }

    public function getAlumnoById($id) {
        $this->db->query("SELECT a.*, gs.grado, gs.seccion FROM alumnos a JOIN grado_secciones gs ON a.grado_seccion_id = gs.id WHERE a.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getAlumnoByToken($token) {
        $this->db->query("SELECT * FROM alumnos WHERE qr_token = :token AND estado = 1");
        $this->db->bind(':token', $token);
        return $this->db->single();
    }

    public function getCount() {
        $this->db->query("SELECT COUNT(*) as total FROM alumnos");
        $row = $this->db->single();
        return $row['total'];
    }

    public function getGradeById($id) {
        $this->db->query("SELECT * FROM grado_secciones WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getAlumnoByDni($dni) {
        $this->db->query("SELECT * FROM alumnos WHERE dni = :dni LIMIT 1");
        $this->db->bind(':dni', $dni);
        return $this->db->single();
    }

    public function actualizarGradoAlumno($id, $gradoId) {
        $this->db->query("UPDATE alumnos SET grado_seccion_id = :grado_id WHERE id = :id");
        $this->db->bind(':grado_id', $gradoId);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
