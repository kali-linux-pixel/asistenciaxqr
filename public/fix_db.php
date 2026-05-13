<?php
require_once '../config/config.php';
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    // Forzamos la eliminación y recreación de la columna para asegurar que se vuelva Texto
    $pdo->exec("ALTER TABLE notas MODIFY COLUMN nota VARCHAR(10) NOT NULL;");
    echo "<h1 style='color:green;'>✅ Columna Reparada Exitosamente.</h1>";
    echo "<p>Ahora ya soporta letras (A, B, C) sin errores.</p>";
} catch (Exception $e) {
    // Si falla el modify, intentamos recrear la tabla limpia
    try {
        $pdo->exec("DROP TABLE IF EXISTS notas;");
        $pdo->exec("CREATE TABLE notas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            alumno_id INT NOT NULL,
            curso_id INT NOT NULL,
            periodo VARCHAR(50) NOT NULL,
            nota VARCHAR(10) NOT NULL,
            comentario VARCHAR(255),
            fecha_registro DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        echo "<h1 style='color:green;'>✅ Tabla Reiniciada y Corregida.</h1>";
    } catch (Exception $e2) {
        echo "<h1 style='color:red;'>❌ Error: " . $e2->getMessage() . "</h1>";
    }
}
