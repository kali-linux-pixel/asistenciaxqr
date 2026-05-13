<?php
class Mensaje {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /** Obtiene la cuenta del Director principal para que el profesor le escriba */
    public function getDirector() {
        $this->db->query("SELECT id, nombre, usuario FROM usuarios WHERE rol = 'director' LIMIT 1");
        return $this->db->single();
    }

    /** Envía un mensaje formal en la base de datos */
    public function enviar($remitente_id, $destinatario_id, $contenido) {
        $this->db->query("INSERT INTO mensajes (remitente_id, destinatario_id, contenido) VALUES (:rem, :dest, :cont)");
        $this->db->bind(':rem', $remitente_id);
        $this->db->bind(':dest', $destinatario_id);
        $this->db->bind(':cont', trim($contenido));
        return $this->db->execute();
    }

    /** Trae el historial de conversación completo entre 2 usuarios */
    public function getConversacion($userA, $userB) {
        $this->db->query("
            SELECT * FROM mensajes 
            WHERE (remitente_id = :uA AND destinatario_id = :uB)
               OR (remitente_id = :uB AND destinatario_id = :uA)
            ORDER BY fecha_envio ASC
        ");
        $this->db->bind(':uA', $userA);
        $this->db->bind(':uB', $userB);
        return $this->db->resultSet();
    }

    /** Marca como leídos todos los mensajes recibidos de una conversación */
    public function marcarLeidos($remitente_id, $destinatario_actual_id) {
        $this->db->query("UPDATE mensajes SET leido = 1 WHERE remitente_id = :rem AND destinatario_id = :dest AND leido = 0");
        $this->db->bind(':rem', $remitente_id);
        $this->db->bind(':dest', $destinatario_actual_id);
        return $this->db->execute();
    }

    /** Cuenta el total de mensajes no leídos que tiene un usuario en particular */
    public function countNoLeidos($userId) {
        $this->db->query("SELECT COUNT(*) as total FROM mensajes WHERE destinatario_id = :uid AND leido = 0");
        $row = $this->db->single();
        return $row['total'] ?? 0;
    }

    /** Retorna la lista de profesores con un resumen del último mensaje e indicador de no leídos */
    public function getProfesoresResumen($directorId) {
        // Traer todos los profesores
        $this->db->query("
            SELECT u.id, u.nombre, u.usuario, gs.grado, gs.seccion,
                   (SELECT COUNT(*) FROM mensajes WHERE remitente_id = u.id AND destinatario_id = :dir AND leido = 0) as no_leidos,
                   (SELECT contenido FROM mensajes 
                    WHERE (remitente_id = u.id AND destinatario_id = :dir) OR (remitente_id = :dir AND destinatario_id = u.id)
                    ORDER BY fecha_envio DESC LIMIT 1) as ultimo_msg,
                   (SELECT fecha_envio FROM mensajes 
                    WHERE (remitente_id = u.id AND destinatario_id = :dir) OR (remitente_id = :dir AND destinatario_id = u.id)
                    ORDER BY fecha_envio DESC LIMIT 1) as fecha_ultimo
            FROM usuarios u
            LEFT JOIN grado_secciones gs ON u.grado_seccion_id = gs.id
            WHERE u.rol = 'profesor'
            ORDER BY fecha_ultimo DESC, u.nombre ASC
        ");
        $this->db->bind(':dir', $directorId);
        return $this->db->resultSet();
    }
}
