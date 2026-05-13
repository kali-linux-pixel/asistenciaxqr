<?php
class Usuario {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /* ---- Login: acepta usuario O email ---- */
    public function login($login, $password) {
        // Intentar con email (tras la migración)
        try {
            $this->db->query(
                "SELECT * FROM usuarios WHERE usuario = :login OR email = :login2 LIMIT 1"
            );
            $this->db->bind(':login',  $login);
            $this->db->bind(':login2', $login);
            $row = $this->db->single();
        } catch (PDOException $e) {
            // Columna email aún no existe — usar solo usuario
            $this->db->query("SELECT * FROM usuarios WHERE usuario = :login LIMIT 1");
            $this->db->bind(':login', $login);
            $row = $this->db->single();
        }

        if ($row && password_verify($password, $row['password'])) {
            return $row;
        }
        return false;
    }

    /* ---- Listado de profesores (solo para Director) ---- */
    public function getProfesores() {
        try {
            $this->db->query(
                "SELECT u.*, gs.grado, gs.seccion
                 FROM usuarios u
                 LEFT JOIN grado_secciones gs ON gs.id = u.grado_seccion_id
                 WHERE u.rol = 'profesor'
                 ORDER BY gs.grado, gs.seccion, u.nombre"
            );
            return $this->db->resultSet();
        } catch (PDOException $e) {
            // Columnas nuevas no existen aún — devolver lista básica
            $this->db->query(
                "SELECT *, NULL as grado, NULL as seccion
                 FROM usuarios
                 WHERE rol IN ('profesor','auxiliar')
                 ORDER BY nombre"
            );
            return $this->db->resultSet();
        }
    }

    /* ---- Un profesor por id ---- */
    public function getById($id) {
        $this->db->query("SELECT * FROM usuarios WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /* ---- Verificar si el email ya existe ---- */
    public function emailExists($email, $excludeId = null) {
        if ($excludeId) {
            $this->db->query("SELECT id FROM usuarios WHERE email = :email AND id != :id");
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM usuarios WHERE email = :email");
        }
        $this->db->bind(':email', $email);
        return (bool) $this->db->single();
    }

    /* ---- Crear profesor (Director action) ---- */
    public function createProfesor($data) {
        $this->db->query(
            "INSERT INTO usuarios (nombre, usuario, email, password, rol, grado_seccion_id)
             VALUES (:nombre, :usuario, :email, :password, 'profesor', :grado_seccion_id)"
        );
        $this->db->bind(':nombre',          $data['nombre']);
        $this->db->bind(':usuario',         $data['usuario']);
        $this->db->bind(':email',           $data['email']);
        $this->db->bind(':password',        password_hash($data['password'], PASSWORD_BCRYPT));
        $this->db->bind(':grado_seccion_id',$data['grado_seccion_id']);
        return $this->db->execute();
    }

    /* ---- Actualizar profesor ---- */
    public function updateProfesor($id, $data) {
        if (!empty($data['password'])) {
            $this->db->query(
                "UPDATE usuarios SET nombre=:nombre, email=:email, password=:password,
                 grado_seccion_id=:grado_seccion_id WHERE id=:id"
            );
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_BCRYPT));
        } else {
            $this->db->query(
                "UPDATE usuarios SET nombre=:nombre, email=:email,
                 grado_seccion_id=:grado_seccion_id WHERE id=:id"
            );
        }
        $this->db->bind(':nombre',           $data['nombre']);
        $this->db->bind(':email',            $data['email']);
        $this->db->bind(':grado_seccion_id', $data['grado_seccion_id']);
        $this->db->bind(':id',               $id);
        return $this->db->execute();
    }

    /* ---- Eliminar profesor ---- */
    public function deleteProfesor($id) {
        try {
            // 1. Eliminar historial de permisos relacionados a este profesor para evitar fallos de FK
            $this->db->query("DELETE FROM permisos WHERE profesor_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();
            
            // 2. Ahora eliminar al profesor de forma segura
            $this->db->query("DELETE FROM usuarios WHERE id = :id AND rol = 'profesor'");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (PDOException $e) {
            // Si ocurre algún otro fallo, retornamos false en vez de crashear
            return false;
        }
    }
}
