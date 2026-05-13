-- ============================================================
-- Ejecuta este script en phpMyAdmin > qr_aula_control
-- ============================================================

USE qr_aula_control;

-- Agregar columna email si no existe
ALTER TABLE usuarios 
    ADD COLUMN IF NOT EXISTS email VARCHAR(150) NULL AFTER usuario,
    ADD COLUMN IF NOT EXISTS grado_seccion_id INT NULL AFTER email;

-- Agregar foreign key solo si no existe
ALTER TABLE usuarios 
    ADD CONSTRAINT FOREIGN KEY IF NOT EXISTS (grado_seccion_id) 
    REFERENCES grado_secciones(id) ON DELETE SET NULL;

-- Cambiar el enum de rol para incluir director/profesor
ALTER TABLE usuarios 
    MODIFY COLUMN rol ENUM('director','profesor') NOT NULL DEFAULT 'profesor';

-- Actualizar el admin existente a director
UPDATE usuarios SET 
    rol   = 'director',
    email = 'director@colegio.edu.pe'
WHERE usuario = 'admin';

-- Renombrar usuario admin a director (opcional)
UPDATE usuarios SET usuario = 'director'
WHERE usuario = 'admin' AND rol = 'director';

-- Cambiar motivo a VARCHAR en permisos
ALTER TABLE permisos MODIFY COLUMN motivo VARCHAR(100) NOT NULL;

-- Confirmar resultado
SELECT id, nombre, usuario, email, rol FROM usuarios;
