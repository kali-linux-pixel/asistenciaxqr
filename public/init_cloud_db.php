<?php
/**
 * INITIALIZER: RESTABLECER BASE DE DATOS A CERO (PRODUCCIÓN RENDER)
 * Ejecuta este archivo desde tu navegador: https://TU-URL.onrender.com/init_cloud_db.php
 * Borrar después de usar.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

$db = new Database();
$results = [];
$err = [];

function runQuery($db, $sql, $desc) {
    global $results, $err;
    try {
        $db->query($sql);
        $db->execute();
        $results[] = "✅ $desc";
    } catch (Exception $e) {
        $err[] = "❌ Error en '$desc': " . $e->getMessage();
    }
}

// 💡 Detectar si es PostgreSQL (Render) o MySQL (Laragon)
$isPostgres = false;
$host = DB_HOST;
if (strpos($host, 'postgres') !== false || strpos($host, '.com') !== false || strpos($host, 'dpg-') !== false) {
    $isPostgres = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_reset'])) {
    
    if ($isPostgres) {
        // --- SECUENCIA POSTGRESQL ---
        
        // 1. Limpiar todo individualmente para evitar errores de comandos múltiples
        $tablesToDrop = ['notas', 'cursos', 'dashboard_config', 'audit_log', 'alertas', 'permisos', 'asistencias', 'alumnos', 'usuarios', 'grado_secciones'];
        foreach ($tablesToDrop as $tbl) {
            runQuery($db, "DROP TABLE IF EXISTS $tbl CASCADE;", "Limpiar tabla vieja: $tbl");
        }

        // 2. Crear Grados
        $sqlGrados = "
            CREATE TABLE grado_secciones (
                id SERIAL PRIMARY KEY,
                grado VARCHAR(50) NOT NULL,
                seccion VARCHAR(10) NOT NULL
            );
        ";
        runQuery($db, $sqlGrados, "Tabla 'grado_secciones'");

        // 3. Crear Usuarios
        $sqlUsers = "
            CREATE TABLE usuarios (
                id SERIAL PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                usuario VARCHAR(100) UNIQUE NOT NULL,
                email VARCHAR(150) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                rol VARCHAR(20) NOT NULL DEFAULT 'profesor',
                grado_seccion_id INTEGER REFERENCES grado_secciones(id) ON DELETE SET NULL
            );
        ";
        runQuery($db, $sqlUsers, "Tabla 'usuarios' (Con Roles y Correo)");

        // 4. Crear Alumnos
        $sqlAlumnos = "
            CREATE TABLE alumnos (
                id SERIAL PRIMARY KEY,
                grado_seccion_id INTEGER REFERENCES grado_secciones(id) ON DELETE CASCADE,
                nombres VARCHAR(100) NOT NULL,
                apellidos VARCHAR(100) NOT NULL,
                dni VARCHAR(20) UNIQUE NOT NULL,
                qr_token VARCHAR(150) UNIQUE NOT NULL,
                estado INTEGER DEFAULT 1
            );
        ";
        runQuery($db, $sqlAlumnos, "Tabla 'alumnos'");

        // 5. Crear Asistencias
        $sqlAsistencias = "
            CREATE TABLE asistencias (
                id SERIAL PRIMARY KEY,
                alumno_id INTEGER REFERENCES alumnos(id) ON DELETE CASCADE,
                fecha DATE DEFAULT CURRENT_DATE,
                hora_entrada TIME DEFAULT CURRENT_TIME,
                hora_salida TIME NULL
            );
        ";
        runQuery($db, $sqlAsistencias, "Tabla 'asistencias'");

        // 6. Crear Permisos
        $sqlPermisos = "
            CREATE TABLE permisos (
                id SERIAL PRIMARY KEY,
                alumno_id INTEGER REFERENCES alumnos(id) ON DELETE CASCADE,
                profesor_id INTEGER REFERENCES usuarios(id) ON DELETE SET NULL,
                motivo VARCHAR(100) NOT NULL,
                fecha DATE DEFAULT CURRENT_DATE,
                hora_salida TIME DEFAULT CURRENT_TIME,
                hora_estimada_retorno TIME NULL,
                hora_retorno TIME NULL,
                estado VARCHAR(20) DEFAULT 'Pendiente'
            );
        ";
        runQuery($db, $sqlPermisos, "Tabla 'permisos'");

        // 7. Alertas
        $sqlAlertas = "
            CREATE TABLE alertas (
                id SERIAL PRIMARY KEY,
                tipo VARCHAR(50) NOT NULL,
                titulo VARCHAR(200) NOT NULL,
                mensaje TEXT NOT NULL,
                para_rol VARCHAR(20) DEFAULT 'director',
                para_usuario_id INTEGER NULL,
                leida SMALLINT DEFAULT 0,
                alumno_id INTEGER NULL,
                link VARCHAR(200) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ";
        runQuery($db, $sqlAlertas, "Tabla 'alertas'");

        // 8. Audit Logs
        $sqlAudit = "
            CREATE TABLE audit_log (
                id SERIAL PRIMARY KEY,
                usuario_id INTEGER NOT NULL,
                usuario_nombre VARCHAR(100),
                accion VARCHAR(100) NOT NULL,
                descripcion TEXT,
                ip VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ";
        runQuery($db, $sqlAudit, "Tabla 'audit_log'");

        // 9. Dashboard Config
        $sqlDash = "
            CREATE TABLE dashboard_config (
                id SERIAL PRIMARY KEY,
                usuario_id INTEGER NOT NULL,
                widget_key VARCHAR(50) NOT NULL,
                visible SMALLINT DEFAULT 1,
                orden INTEGER DEFAULT 0,
                UNIQUE (usuario_id, widget_key)
            );
        ";
        runQuery($db, $sqlDash, "Tabla 'dashboard_config'");

        // 10. Cursos
        $sqlCursos = "
            CREATE TABLE cursos (
                id SERIAL PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                descripcion TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ";
        runQuery($db, $sqlCursos, "Tabla 'cursos'");

        // 11. Notas
        $sqlNotas = "
            CREATE TABLE notas (
                id SERIAL PRIMARY KEY,
                alumno_id INTEGER REFERENCES alumnos(id) ON DELETE CASCADE,
                curso_id INTEGER REFERENCES cursos(id) ON DELETE CASCADE,
                periodo VARCHAR(50) NOT NULL,
                nota VARCHAR(10) NOT NULL,
                comentario VARCHAR(255) NULL,
                fecha_registro DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ";
        runQuery($db, $sqlNotas, "Tabla 'notas'");

    } else {
        // --- SECUENCIA MYSQL (LOCAL) ---
        
        runQuery($db, "SET FOREIGN_KEY_CHECKS = 0;", "Desactivar Checks Foráneos");
        
        $tables = ['notas', 'cursos', 'dashboard_config', 'audit_log', 'alertas', 'permisos', 'asistencias', 'alumnos', 'usuarios', 'grado_secciones'];
        foreach ($tables as $tbl) {
            runQuery($db, "DROP TABLE IF EXISTS $tbl;", "Eliminar tabla $tbl");
        }
        
        runQuery($db, "SET FOREIGN_KEY_CHECKS = 1;", "Reactivar Checks Foráneos");

        runQuery($db, "
            CREATE TABLE grado_secciones (
                id INT AUTO_INCREMENT PRIMARY KEY,
                grado VARCHAR(50) NOT NULL,
                seccion VARCHAR(10) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ", "Tabla 'grado_secciones'");

        runQuery($db, "
            CREATE TABLE usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                usuario VARCHAR(100) UNIQUE NOT NULL,
                email VARCHAR(150) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                rol ENUM('director','profesor') NOT NULL DEFAULT 'profesor',
                grado_seccion_id INT NULL,
                FOREIGN KEY (grado_seccion_id) REFERENCES grado_secciones(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ", "Tabla 'usuarios'");

        runQuery($db, "
            CREATE TABLE alumnos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                grado_seccion_id INT NOT NULL,
                nombres VARCHAR(100) NOT NULL,
                apellidos VARCHAR(100) NOT NULL,
                dni VARCHAR(20) UNIQUE NOT NULL,
                qr_token VARCHAR(150) UNIQUE NOT NULL,
                estado TINYINT DEFAULT 1,
                FOREIGN KEY (grado_seccion_id) REFERENCES grado_secciones(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ", "Tabla 'alumnos'");

        runQuery($db, "
            CREATE TABLE asistencias (
                id INT AUTO_INCREMENT PRIMARY KEY,
                alumno_id INT NOT NULL,
                fecha DATE NOT NULL,
                hora_entrada TIME NOT NULL,
                hora_salida TIME NULL,
                FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ", "Tabla 'asistencias'");

        runQuery($db, "
            CREATE TABLE permisos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                alumno_id INT NOT NULL,
                profesor_id INT NULL,
                motivo VARCHAR(100) NOT NULL,
                fecha DATE NOT NULL,
                hora_salida TIME NOT NULL,
                hora_estimada_retorno TIME NULL,
                hora_retorno TIME NULL,
                estado VARCHAR(20) DEFAULT 'Pendiente',
                FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
                FOREIGN KEY (profesor_id) REFERENCES usuarios(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ", "Tabla 'permisos'");

        runQuery($db, "
            CREATE TABLE alertas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tipo VARCHAR(50) NOT NULL,
                titulo VARCHAR(200) NOT NULL,
                mensaje TEXT NOT NULL,
                para_rol ENUM('director','profesor','ambos') DEFAULT 'director',
                para_usuario_id INT NULL,
                leida TINYINT(1) DEFAULT 0,
                alumno_id INT NULL,
                link VARCHAR(200) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ", "Tabla 'alertas'");

        runQuery($db, "
            CREATE TABLE audit_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                usuario_nombre VARCHAR(100),
                accion VARCHAR(100) NOT NULL,
                descripcion TEXT,
                ip VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ", "Tabla 'audit_log'");

        runQuery($db, "
            CREATE TABLE dashboard_config (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                widget_key VARCHAR(50) NOT NULL,
                visible TINYINT(1) DEFAULT 1,
                orden INT DEFAULT 0,
                UNIQUE KEY (usuario_id, widget_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ", "Tabla 'dashboard_config'");

        runQuery($db, "
            CREATE TABLE cursos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                descripcion TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ", "Tabla 'cursos'");

        runQuery($db, "
            CREATE TABLE notas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                alumno_id INT NOT NULL,
                curso_id INT NOT NULL,
                periodo VARCHAR(50) NOT NULL,
                nota VARCHAR(10) NOT NULL,
                comentario VARCHAR(255) NULL,
                fecha_registro DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
                FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ", "Tabla 'notas'");
    }

    // --- SEEDING COMÚN (INSTALACIÓN DE VALORES POR DEFECTO) ---
    
    // 1. Sembrar Grados
    $gradosSeed = "INSERT INTO grado_secciones (grado, seccion) VALUES 
        ('1er Grado', 'A'), ('1er Grado', 'B'),
        ('2do Grado', 'A'), ('2do Grado', 'B'),
        ('3er Grado', 'A'), ('3er Grado', 'B'),
        ('4to Grado', 'A'), ('4to Grado', 'B'),
        ('5to Grado', 'A'), ('5to Grado', 'B')";
    runQuery($db, $gradosSeed, "Cargar Grados y Secciones predeterminados (1° a 5°)");

    // 2. Sembrar Cursos
    $cursosSeed = "INSERT INTO cursos (nombre, descripcion) VALUES 
        ('Matemáticas', 'Lógica y Álgebra'),
        ('Comunicación', 'Lenguaje y Literatura'),
        ('Ciencia y Tecnología', 'Ciencias Naturales'),
        ('Educación Física', 'Desarrollo corporal'),
        ('Inglés', 'Idioma Extranjero')";
    runQuery($db, $cursosSeed, "Cargar Cursos base del sistema");

    // 3. Sembrar Director General (Solicitado por el usuario)
    $pwdHash = password_hash('admin123', PASSWORD_DEFAULT);
    $sqlDirector = "INSERT INTO usuarios (nombre, usuario, email, password, rol) VALUES (
        'Director General',
        'director@colegio.edu.pe',
        'director@colegio.edu.pe',
        '$pwdHash',
        'director'
    )";
    runQuery($db, $sqlDirector, "Crear cuenta única del DIRECTOR (director@colegio.edu.pe / admin123)");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud Deployer — QR Aula Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --azul: #0B0B93; --amarillo: #FBBF24; --rojo: #DC2626; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f4f6fb; color: #1e293b; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; }
        .container { background: #fff; border: 4px solid var(--azul); border-radius: 24px; box-shadow: 0 15px 0 var(--azul); width: 100%; max-width: 600px; padding: 2.5rem; text-align: center; position: relative; }
        .container::after { content: ''; position: absolute; top: -4px; left: -4px; right: -4px; height: 8px; background: var(--amarillo); border-radius: 24px 24px 0 0; }
        .engine-badge { display: inline-flex; align-items: center; gap: 8px; padding: 6px 16px; border-radius: 99px; font-size: 0.75rem; font-weight: 900; text-transform: uppercase; border: 2px solid var(--azul); margin-bottom: 1.5rem; color: var(--azul); }
        h1 { color: var(--azul); margin: 0 0 0.5rem 0; font-size: 1.6rem; font-weight: 900; text-transform: uppercase; }
        p.desc { color: #64748b; margin: 0 0 2rem 0; font-size: 0.92rem; font-weight: 500; }
        .btn-init { display: inline-flex; align-items: center; gap: 10px; background: var(--rojo); color: #fff; border: none; padding: 16px 30px; border-radius: 16px; font-size: 1rem; font-weight: 900; text-transform: uppercase; cursor: pointer; transition: all 0.2s; border-bottom: 6px solid #991b1b; }
        .btn-init:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(220,38,38,0.3); }
        .btn-init:active { transform: translateY(0); border-bottom-width: 2px; }
        .results { text-align: left; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; margin-top: 2rem; max-height: 300px; overflow-y: auto; }
        .item { font-size: 0.85rem; font-weight: 700; padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .item:last-child { border-bottom: none; }
        .item.ok { color: #15803d; }
        .item.err { color: var(--rojo); background: #fef2f2; border-radius: 8px; }
        .credentials { background: #fffbeb; border: 2px solid var(--amarillo); border-radius: 16px; padding: 1.25rem; text-align: left; margin-top: 1.5rem; display: flex; align-items: center; gap: 15px; }
        .creds-icon { font-size: 2rem; color: var(--amarillo); }
        .btn-login { display: block; background: var(--azul); color: #fff; padding: 14px; text-decoration: none; border-radius: 12px; font-weight: 800; text-transform: uppercase; margin-top: 1.5rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="engine-badge">
        <i class="fa-solid fa-server"></i> Motor Detectado: <?php echo $isPostgres ? 'PostgreSQL (Cloud)' : 'MySQL (Local)'; ?>
    </div>
    
    <h1>Reset Base de Datos</h1>
    <p class="desc">Este script limpiará absolutamente toda la información existente e instalará la estructura limpia con el Director solicitado.</p>

    <?php if (empty($results) && empty($err)): ?>
        <form method="POST" onsubmit="return confirm('¿Estás seguro? Esta acción borrará TODA la información de la base de datos.');">
            <input type="hidden" name="confirm_reset" value="1">
            <button type="submit" class="btn-init">
                <i class="fa-solid fa-triangle-exclamation"></i> 
                Instalar Base Limpia
            </button>
        </form>
    <?php else: ?>
        <div class="results">
            <?php foreach ($results as $r): ?>
                <div class="item ok"><?php echo $r; ?></div>
            <?php endforeach; ?>
            <?php foreach ($err as $e): ?>
                <div class="item err"><?php echo $e; ?></div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($err)): ?>
            <div class="credentials">
                <div class="creds-icon"><i class="fa-solid fa-lock"></i></div>
                <div>
                    <div style="font-weight:900; color:var(--azul); font-size:0.9rem; text-transform:uppercase;">Acceso Director Creado</div>
                    <div style="font-size:0.82rem; color:#475569; margin-top:4px;">
                        <strong>Usuario/Email:</strong> director@colegio.edu.pe<br>
                        <strong>Contraseña:</strong> admin123
                    </div>
                </div>
            </div>
            
            <a href="<?php echo URLROOT; ?>/usuarios/login" class="btn-login">
                Ir al Inicio de Sesión <i class="fa-solid fa-arrow-right"></i>
            </a>
            <p style="font-size:0.72rem; color:#ef4444; margin-top:15px; font-weight:700;">
                ⚠️ BORRA ESTE ARCHIVO (public/init_cloud_db.php) POR SEGURIDAD.
            </p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
