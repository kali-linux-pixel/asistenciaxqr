CREATE DATABASE IF NOT EXISTS qr_aula_control CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qr_aula_control;

CREATE TABLE IF NOT EXISTS grado_secciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grado VARCHAR(20) NOT NULL,
    seccion VARCHAR(10) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS alumnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    dni VARCHAR(15),
    grado_seccion_id INT NOT NULL,
    qr_token VARCHAR(255) NOT NULL UNIQUE,
    estado TINYINT DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grado_seccion_id) REFERENCES grado_secciones(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'profesor', 'auxiliar') NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id INT NOT NULL,
    fecha DATE DEFAULT (CURRENT_DATE),
    hora_entrada TIME DEFAULT (CURRENT_TIME),
    hora_salida TIME NULL,
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS permisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id INT NOT NULL,
    profesor_id INT NOT NULL,
    motivo ENUM('Baño', 'Salud', 'Dirección', 'Otro') NOT NULL,
    fecha DATE DEFAULT (CURRENT_DATE),
    hora_salida TIME DEFAULT (CURRENT_TIME),
    hora_retorno TIME NULL,
    estado ENUM('Pendiente', 'Retornado') DEFAULT 'Pendiente',
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id),
    FOREIGN KEY (profesor_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Insert standard groups
INSERT INTO grado_secciones (grado, seccion) VALUES 
('1ro', 'A'), ('1ro', 'B'),
('2do', 'A'), ('2do', 'B'),
('3ro', 'A'), ('3ro', 'B'),
('4to', 'A'), ('4to', 'B'),
('5to', 'A'), ('5to', 'B');

-- Insert initial admin user (password is 'admin123')
INSERT INTO usuarios (nombre, usuario, password, rol) VALUES 
('Administrador Principal', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
