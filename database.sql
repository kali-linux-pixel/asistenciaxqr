-- ============================================================
-- MIGRACIÓN COMPLETA: Sistema de Roles Director / Profesor
-- Ejecutar en PostgreSQL (Render) o MySQL (Local)
-- ============================================================

-- Para PostgreSQL (Render):
-- Agregar columnas si no existen
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='usuarios' AND column_name='email') THEN
        ALTER TABLE usuarios ADD COLUMN email VARCHAR(150) UNIQUE;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='usuarios' AND column_name='grado_seccion_id') THEN
        ALTER TABLE usuarios ADD COLUMN grado_seccion_id INT REFERENCES grado_secciones(id) ON DELETE SET NULL;
    END IF;
END $$;

-- Cambiar el tipo del enum rol (PostgreSQL no tiene ALTER ENUM directo)
-- Primero crear el nuevo tipo
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'rol_tipo') THEN
        CREATE TYPE rol_tipo AS ENUM ('director', 'profesor');
    END IF;
END $$;

-- Actualizar la columna rol
ALTER TABLE usuarios ALTER COLUMN rol TYPE VARCHAR(20);
UPDATE usuarios SET rol = 'director' WHERE rol IN ('admin', 'administrador');
UPDATE usuarios SET rol = 'profesor' WHERE rol NOT IN ('director');

-- Insertar el Director si no existe
INSERT INTO usuarios (nombre, usuario, email, password, rol, grado_seccion_id)
VALUES (
    'Director General',
    'director',
    'director@colegio.edu.pe',
    '$2y$10$RXp3Hfzk7p.FVu2vlBzqBOj/RUlN/YuoAIXHWB0HZaR.vHZc8NF6',
    'director',
    NULL
)
ON CONFLICT (usuario) DO UPDATE SET rol = 'director', email = EXCLUDED.email;

-- ============================================================
-- Para MySQL (Laragon local) — ejecutar esto en su lugar:
-- ============================================================
/*
ALTER TABLE usuarios 
    ADD COLUMN IF NOT EXISTS email VARCHAR(150) UNIQUE AFTER usuario,
    ADD COLUMN IF NOT EXISTS grado_seccion_id INT NULL AFTER email,
    MODIFY COLUMN rol ENUM('director','profesor') NOT NULL DEFAULT 'profesor';

ALTER TABLE usuarios 
    ADD FOREIGN KEY IF NOT EXISTS fk_user_grado (grado_seccion_id) REFERENCES grado_secciones(id) ON DELETE SET NULL;

ALTER TABLE permisos MODIFY COLUMN motivo VARCHAR(100) NOT NULL;

-- Actualizar rol del admin existente a director
UPDATE usuarios SET rol = 'director', email = 'director@colegio.edu.pe' WHERE usuario = 'admin' OR rol = 'admin';

INSERT IGNORE INTO usuarios (nombre, usuario, email, password, rol) VALUES 
('Director General', 'director', 'director@colegio.edu.pe', '$2y$10$RXp3Hfzk7p.FVu2vlBzqBOj/RUlN/YuoAIXHWB0HZaR.vHZc8NF6', 'director');
*/
