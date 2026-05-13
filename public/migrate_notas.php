<?php
require_once '../config/config.php';

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h1>🔧 Migración de Módulo de Calificaciones</h1>";

    function run($pdo, $sql, $desc) {
        try {
            $pdo->exec($sql);
            echo "<div style='color:green; font-family:sans-serif; margin-bottom:8px;'>✅ $desc ejecutado con éxito.</div>";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "<div style='color:orange; font-family:sans-serif; margin-bottom:8px;'>⏭️ $desc ya existía (omitido).</div>";
            } else {
                echo "<div style='color:red; font-family:sans-serif; margin-bottom:8px;'>❌ $desc falló: " . $e->getMessage() . "</div>";
            }
        }
    }

    // 1. Crear tabla de Cursos
    run($pdo, "CREATE TABLE IF NOT EXISTS cursos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;", "Crear tabla de Cursos");

    // 2. Crear tabla de Notas (Usamos VARCHAR para soportar AD, A, B, C y Números)
    run($pdo, "CREATE TABLE IF NOT EXISTS notas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        alumno_id INT NOT NULL,
        curso_id INT NOT NULL,
        periodo VARCHAR(50) NOT NULL,
        nota VARCHAR(5) NOT NULL,
        comentario VARCHAR(255),
        fecha_registro DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
        FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;", "Crear tabla de Notas");

    // Intentar alterar por si ya se había creado como DECIMAL antes
    run($pdo, "ALTER TABLE notas MODIFY COLUMN nota VARCHAR(5) NOT NULL", "Actualizar tipo de nota a Texto (soporte letras/números)");

    // 3. Insertar algunos cursos de ejemplo si no hay
    $check = $pdo->query("SELECT COUNT(*) FROM cursos")->fetchColumn();
    if ($check == 0) {
        run($pdo, "INSERT INTO cursos (nombre, descripcion) VALUES 
        ('Matemáticas', 'Curso general de lógica y matemáticas'),
        ('Comunicación', 'Lenguaje y literatura'),
        ('Ciencia y Tecnología', 'Biología, Física, Química'),
        ('Historia y Geografía', 'Ciencias Sociales'),
        ('Inglés', 'Idioma extranjero')", "Insertar cursos base predeterminados");
    }

    echo "<hr><h3>🚀 Base lista. Procediendo con la implementación del panel visual...</h3>";

} catch (PDOException $e) {
    echo "<h2 style='color:red;'>❌ Error fatal: " . $e->getMessage() . "</h2>";
}
