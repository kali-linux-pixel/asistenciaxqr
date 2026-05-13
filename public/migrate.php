<?php
/**
 * MIGRACIÓN AUTOMÁTICA — Sistema de Roles
 * Ejecuta: http://localhost/AsistenciaxQr/public/migrate.php
 * BORRA ESTE ARCHIVO DESPUÉS DE USARLO
 */
require_once '../app/boot.php';

$db = new Database();
$pasos = [];
$errores = [];

function run($db, $sql, $desc) {
    global $pasos, $errores;
    try {
        $db->query($sql);
        $db->execute();
        $pasos[] = "✅ $desc";
    } catch (PDOException $e) {
        // Ignorar errores de "ya existe" (Duplicate column, etc.)
        $msg = $e->getMessage();
        if (
            strpos($msg, 'Duplicate column') !== false ||
            strpos($msg, 'already exists') !== false ||
            strpos($msg, 'Multiple primary key') !== false
        ) {
            $pasos[] = "⏭️ $desc (ya existe, saltado)";
        } else {
            $errores[] = "❌ $desc → " . $msg;
        }
    }
}

// 1. Agregar columna email
run($db,
    "ALTER TABLE usuarios ADD COLUMN email VARCHAR(150) NULL UNIQUE AFTER usuario",
    "Agregar columna email"
);

// 2. Agregar columna grado_seccion_id
run($db,
    "ALTER TABLE usuarios ADD COLUMN grado_seccion_id INT NULL AFTER email",
    "Agregar columna grado_seccion_id"
);

// 3. Agregar foreign key
run($db,
    "ALTER TABLE usuarios ADD CONSTRAINT fk_user_grado FOREIGN KEY (grado_seccion_id) REFERENCES grado_secciones(id) ON DELETE SET NULL",
    "Agregar foreign key grado_seccion_id"
);

// 4. Paso intermedio: Cambiar rol a VARCHAR para evitar truncation error
run($db,
    "ALTER TABLE usuarios MODIFY COLUMN rol VARCHAR(50) NOT NULL DEFAULT 'profesor'",
    "Cambiar rol temporalmente a VARCHAR"
);

// 5. Actualizar admin existente → director (mantiene contraseña original)
run($db,
    "UPDATE usuarios SET rol='director', email='director@colegio.edu.pe' WHERE usuario='admin' OR rol='admin'",
    "Actualizar cuenta admin → director (datos actualizados)"
);

// 6. Paso final: Cambiar rol al ENUM restringido
run($db,
    "ALTER TABLE usuarios MODIFY COLUMN rol ENUM('director','profesor') NOT NULL DEFAULT 'profesor'",
    "Actualizar ENUM definitivo de rol (director/profesor)"
);

// 7. Renombrar usuario admin → director
run($db,
    "UPDATE usuarios SET usuario='director' WHERE usuario='admin'",
    "Renombrar usuario admin → director"
);

// 8. Ampliar campo motivo en permisos
run($db,
    "ALTER TABLE permisos MODIFY COLUMN motivo VARCHAR(100) NOT NULL",
    "Ampliar campo motivo en permisos"
);

// Verificar resultado
try {
    $db->query("SELECT id, nombre, usuario, email, rol, grado_seccion_id FROM usuarios");
    $usuarios = $db->resultSet();
} catch (Exception $e) {
    $usuarios = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Migración — <?php echo SITENAME; ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6fb; padding: 2rem; }
        .box { max-width: 700px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h1 { color: #0B0B93; font-size: 1.4rem; margin-bottom: 0.5rem; }
        .step { padding: 8px 14px; border-radius: 8px; margin: 6px 0; font-size: 0.9rem; }
        .step.ok  { background: #e8f8f0; color: #1a7a48; }
        .step.err { background: #fdecea; color: #c0392b; }
        .step.skip{ background: #f0f0f0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; font-size: 0.85rem; }
        th { background: #0B0B93; color: #fff; padding: 8px 12px; text-align: left; }
        td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; }
        .btn { display: inline-block; padding: 10px 20px; background: #0B0B93; color: #fff; border-radius: 9px; text-decoration: none; font-weight: 700; margin-top: 1.5rem; }
        .warn { background: #fff0cc; border: 1px solid #f39c12; border-radius: 8px; padding: 10px 14px; font-size: 0.82rem; color: #8a5d00; margin-top: 1rem; }
    </style>
</head>
<body>
<div class="box">
    <h1>🔧 Migración de Base de Datos</h1>
    <p style="color:#6b7280; margin-bottom:1.5rem;">Sistema de Roles — <?php echo SITENAME; ?></p>

    <?php foreach($pasos as $p): ?>
        <?php $cls = strpos($p,'✅')!==false ? 'ok' : (strpos($p,'❌')!==false ? 'err' : 'skip'); ?>
        <div class="step <?php echo $cls; ?>"><?php echo $p; ?></div>
    <?php endforeach; ?>

    <?php foreach($errores as $e): ?>
        <div class="step err"><?php echo $e; ?></div>
    <?php endforeach; ?>

    <?php if(!empty($usuarios)): ?>
    <h2 style="margin-top:1.5rem; font-size:1rem; color:#374151;">Usuarios en el sistema:</h2>
    <table>
        <tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Email</th><th>Rol</th></tr>
        <?php foreach($usuarios as $u): ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['nombre']); ?></td>
            <td><?php echo htmlspecialchars($u['usuario']); ?></td>
            <td><?php echo htmlspecialchars($u['email'] ?? '—'); ?></td>
            <td><strong style="color:<?php echo $u['rol']==='director'?'#0B0B93':'#27ae60'; ?>"><?php echo $u['rol']; ?></strong></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>

    <div class="warn">
        ⚠️ <strong>Importante:</strong> Elimina este archivo después de usarlo por seguridad.<br>
        Ruta: <code>C:\laragon\www\AsistenciaxQr\public\migrate.php</code>
    </div>

    <?php if(empty($errores)): ?>
        <a href="<?php echo URLROOT; ?>/usuarios/login" class="btn">✅ Ir al Login</a>
    <?php else: ?>
        <p style="color:#c0392b; margin-top:1rem; font-weight:700;">Hubo errores. Revisa los detalles arriba.</p>
    <?php endif; ?>
</div>
</body>
</html>
