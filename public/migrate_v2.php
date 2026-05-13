<?php
require_once '../config/config.php';
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $results = [];

    // 1. Tabla de alertas/notificaciones internas
    $pdo->exec("CREATE TABLE IF NOT EXISTS alertas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tipo VARCHAR(50) NOT NULL COMMENT 'riesgo_asistencia, notas_pendientes, papeleta, etc.',
        titulo VARCHAR(200) NOT NULL,
        mensaje TEXT NOT NULL,
        para_rol ENUM('director','profesor','ambos') DEFAULT 'director',
        para_usuario_id INT NULL COMMENT 'NULL = todos del rol',
        leida TINYINT(1) DEFAULT 0,
        alumno_id INT NULL,
        link VARCHAR(200) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $results[] = "✅ Tabla 'alertas' creada";

    // 2. Tabla de log de auditoría
    $pdo->exec("CREATE TABLE IF NOT EXISTS audit_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        usuario_nombre VARCHAR(100),
        accion VARCHAR(100) NOT NULL,
        descripcion TEXT,
        ip VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $results[] = "✅ Tabla 'audit_log' creada";

    // 3. Tabla de widgets personalizables del dashboard
    $pdo->exec("CREATE TABLE IF NOT EXISTS dashboard_config (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        widget_key VARCHAR(50) NOT NULL,
        visible TINYINT(1) DEFAULT 1,
        orden INT DEFAULT 0,
        UNIQUE KEY (usuario_id, widget_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $results[] = "✅ Tabla 'dashboard_config' creada";

    echo "<div style='font-family:sans-serif; max-width:600px; margin:50px auto; padding:30px; background:#f8fafc; border-radius:12px;'>";
    echo "<h1 style='color:#0B0B93;'>🚀 Migración de Nuevas Funciones</h1>";
    foreach($results as $r) {
        echo "<p style='background:#fff; padding:10px 15px; border-radius:8px; border-left:4px solid green;'>$r</p>";
    }
    echo "<br><a href='".URLROOT."/pages/index' style='background:#0B0B93; color:#fff; padding:12px 24px; border-radius:8px; text-decoration:none; font-weight:700;'>← Volver al Dashboard</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='font-family:sans-serif; padding:30px; background:#fdecea;'>";
    echo "<h2 style='color:red;'>❌ Error:</h2><pre>" . $e->getMessage() . "</pre>";
    echo "</div>";
}
