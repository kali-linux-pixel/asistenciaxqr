<?php
/*
 * PDO Database Class
 * Connect to database
 * Create prepared statements
 * Bind values
 * Return rows and results
 */
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;

    public function __construct() {
        $host = DB_HOST;
        $dbname = DB_NAME;
        $user = DB_USER;
        $pass = DB_PASS;

        // Auto-Detect Driver: If deploying to Render (contains postgres, render or dpg-) use pgsql, else local mysql
        if (strpos($host, 'postgres') !== false || strpos($host, '.com') !== false || strpos($host, 'dpg-') !== false) {
            $dsn = 'pgsql:host=' . $host . ';port=5432;dbname=' . $dbname;
        } else {
            $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
        }

        $options = array(
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            $this->dbh = new PDO($dsn, $user, $pass, $options);
            
            // --- FORCE LIMA TIMEZONE FOR BOTH ENGINES ---
            if (strpos($dsn, 'pgsql') !== false) {
                $this->dbh->exec("SET timezone TO 'America/Lima';");
                $this->initPostgresSchema();
            } else {
                $this->dbh->exec("SET time_zone = '-05:00';");
            }
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            echo "Conexion Fallida: " . $this->error;
            die();
        }
    }

    // Auto-Installer for PostgreSQL on Render
    private function initPostgresSchema() {
        $this->query("SELECT 1 FROM information_schema.tables WHERE table_name = 'usuarios'");
        try {
            $exists = $this->single();
        } catch (Exception $e) { $exists = false; }

        if (!$exists) {
            // Create tables using Postgres Syntax
            $sql = "
            CREATE TABLE IF NOT EXISTS usuarios (
                id SERIAL PRIMARY KEY,
                nombre VARCHAR(100),
                usuario VARCHAR(50) UNIQUE,
                password VARCHAR(255)
            );
            CREATE TABLE IF NOT EXISTS grado_secciones (
                id SERIAL PRIMARY KEY,
                grado VARCHAR(50),
                seccion VARCHAR(10)
            );
            CREATE TABLE IF NOT EXISTS alumnos (
                id SERIAL PRIMARY KEY,
                grado_seccion_id INTEGER REFERENCES grado_secciones(id),
                nombres VARCHAR(100),
                apellidos VARCHAR(100),
                dni VARCHAR(20),
                qr_token VARCHAR(100) UNIQUE,
                estado INTEGER DEFAULT 1
            );
            CREATE TABLE IF NOT EXISTS asistencias (
                id SERIAL PRIMARY KEY,
                alumno_id INTEGER REFERENCES alumnos(id),
                fecha DATE DEFAULT CURRENT_DATE,
                hora_entrada TIME DEFAULT CURRENT_TIME,
                hora_salida TIME NULL
            );
            CREATE TABLE IF NOT EXISTS permisos (
                id SERIAL PRIMARY KEY,
                alumno_id INTEGER REFERENCES alumnos(id),
                profesor_id INTEGER REFERENCES usuarios(id),
                motivo VARCHAR(50),
                fecha DATE DEFAULT CURRENT_DATE,
                hora_salida TIME DEFAULT CURRENT_TIME,
                hora_estimada_retorno TIME NULL,
                hora_retorno TIME NULL,
                estado VARCHAR(20) DEFAULT 'Pendiente'
            );
            ";
            $this->dbh->exec($sql);

            // Seeding in standard format after tables created
            $this->query("SELECT COUNT(*) as total FROM usuarios");
            $c = $this->single();
            if ($c['total'] == 0) {
                $hash = password_hash('admin123', PASSWORD_DEFAULT);
                $this->dbh->exec("INSERT INTO usuarios (nombre, usuario, password) VALUES ('Admin Sistema', 'admin', '$hash')");
                $this->dbh->exec("INSERT INTO grado_secciones (grado, seccion) VALUES ('1er Año', 'A'), ('1er Año', 'B'), ('2do Año', 'A'), ('2do Año', 'B')");
            }
        }
    }

    // Prepare statement with query
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Bind values
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Execute the prepared statement
    public function execute() {
        return $this->stmt->execute();
    }

    // Get result set as array of objects/assoc
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    // Get single record
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }

    // Get row count
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    // Insert ID
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
}
