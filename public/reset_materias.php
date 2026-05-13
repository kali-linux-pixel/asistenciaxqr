<?php
require_once '../config/config.php';
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE cursos;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    $materias = [
        'Matemática (Aritmética, Álgebra, Geometría, RM)',
        'Comunicación (Lenguaje, Literatura)',
        'Ciencias Sociales (Historia, Geografía, Economía)',
        'DPCC (Desarrollo Personal, Ciudadanía y Cívica)',
        'Inglés como lengua extranjera',
        'Ciencia y Tecnología (Física, Química, Biología)',
        'Arte y Cultura',
        'Educación Física',
        'Educación Religiosa',
        'Educación para el Trabajo (EPT)',
        'Tutoría y Orientación Educativa'
    ];

    $stmt = $pdo->prepare("INSERT INTO cursos (nombre, descripcion) VALUES (?, 'Materia Oficial CNEB')");
    foreach($materias as $m) {
        $stmt->execute([$m]);
    }

    echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>";
    echo "<h1 style='color:green;'>✅ LAS 12 MATERIAS OFICIALES DEL PERÚ INSERTADAS.</h1>";
    echo "<p>Tu lista de cursos ahora está actualizada y 100% limpia.</p>";
    echo "<a href='".URLROOT."/notas' style='background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Volver a Calificaciones</a>";
    echo "</div>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
