<?php
class Curso {
    private $db;
    public function __construct() { $this->db = new Database; }

    public function getCursos() {
        $this->db->query("SELECT * FROM cursos ORDER BY nombre ASC");
        return $this->db->resultSet();
    }

    public function getCursoById($id) {
        $this->db->query("SELECT * FROM cursos WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function agregar($nombre, $desc) {
        $this->db->query("INSERT INTO cursos (nombre, descripcion) VALUES (:nombre, :desc)");
        $this->db->bind(':nombre', $nombre);
        $this->db->bind(':desc', $desc);
        return $this->db->execute();
    }
}
