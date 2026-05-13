<?php
class Alumnos extends Controller {
    protected $alumnoModel;

    public function __construct() {
        if (!isLoggedIn()) { redirect('usuarios/login'); }
        $this->alumnoModel = $this->model('Alumno');
    }

    public function index() {
        $isDir = isDirector();
        if ($isDir) {
            // Director ve a todos los alumnos de todos los grados
            $alumnos = $this->alumnoModel->getAlumnos();
        } else {
            // Profesor ve exclusivamente a los alumnos de su aula asignada
            $gradoId = $_SESSION['user_grado_seccion_id'] ?? 0;
            $alumnos = $this->alumnoModel->getAlumnosByGrado($gradoId);
        }

        $data = [
            'title' => $isDir ? 'Gestión Global de Alumnos' : 'Mi Aula: Relación de Alumnos',
            'alumnos' => $alumnos,
            'isDirector' => $isDir
        ];
        $this->view('alumnos/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST array
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $targetGrado = trim($_POST['grado_seccion']);
            // REGLA DOCENTE: Si no es Director, forzar a su propia sección asignada
            if (!isDirector()) {
                $targetGrado = $_SESSION['user_grado_seccion_id'] ?? 0;
            }

            $data = [
                'nombres' => trim($_POST['nombres']),
                'apellidos' => trim($_POST['apellidos']),
                'dni' => trim($_POST['dni']),
                'grado_seccion' => $targetGrado,
                'grades' => $this->alumnoModel->getGrades(),
                'error' => ''
            ];

            if ($this->alumnoModel->registrarAlumno($data)) {
                flash('alumno_message', 'Alumno registrado con éxito');
                redirect('alumnos');
            } else {
                die('Algo salió mal');
            }

        } else {
            $grades = $this->alumnoModel->getGrades();
            $data = [
                'title' => 'Nuevo Alumno',
                'grades' => $grades
            ];
            $this->view('alumnos/add', $data);
        }
    }

    public function import() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!empty($_FILES['csv_file']['tmp_name'])) {
                // Leer el contenido completo
                $content = file_get_contents($_FILES['csv_file']['tmp_name']);
                
                // Detectar codificación actual para convertir a UTF-8 y evitar errores de acentos (como Pérez)
                $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'WINDOWS-1252'], true);
                if ($encoding !== 'UTF-8') {
                    $content = mb_convert_encoding($content, 'UTF-8', $encoding ? $encoding : 'ISO-8859-1');
                }

                // Limpiar carácter BOM de Excel si existe
                $bom = pack('H*','EFBBBF');
                $content = preg_replace("/^$bom/", '', $content);
                
                $tmpHandle = fopen('php://temp', 'r+');
                fwrite($tmpHandle, $content);
                rewind($tmpHandle);

                // Detectar si usa coma o punto y coma
                $firstLine = fgets($tmpHandle);
                rewind($tmpHandle);
                $separator = (strpos($firstLine, ';') !== false) ? ';' : ',';

                // Saltar cabecera
                fgetcsv($tmpHandle, 1000, $separator);
                
                $successCount   = 0;
                $updateCount    = 0;
                $skipCount      = 0;
                $otherClassCount = 0; // NUEVO: Contador para filas ajenas
                $errorCount     = 0;

                while (($row = fgetcsv($tmpHandle, 1000, $separator)) !== FALSE) {
                    // Ignorar filas vacías
                    if (empty($row) || count($row) < 3) continue;

                    $apellidos = trim($row[0] ?? '');
                    $nombres   = trim($row[1] ?? '');
                    $dni       = trim($row[2] ?? '');
                    $gradoStr  = trim($row[3] ?? '');
                    $seccStr   = trim($row[4] ?? '');

                    if (empty($nombres) || empty($apellidos) || empty($dni)) {
                        continue; // Fila inválida o vacía
                    }

                    // Intentar resolver grado y sección
                    $gradoId = $this->alumnoModel->getGradeIdByNames($gradoStr, $seccStr);

                    if ($gradoId) {
                        // ==========================================
                        // REGLA ROL DOCENTE: Filtrar por su propia aula
                        // ==========================================
                        if (!isDirector()) {
                            $teacherGradoId = $_SESSION['user_grado_seccion_id'] ?? 0;
                            if ($gradoId != $teacherGradoId) {
                                $otherClassCount++;
                                continue; // Ignorar esta fila de inmediato
                            }
                        }

                        // REGLA DE INTEGRIDAD: ¿Ya existe un alumno con ese DNI?
                        $alumnoExistente = $this->alumnoModel->getAlumnoByDni($dni);

                        if ($alumnoExistente) {
                            // Ya existe: ¿Su aula es diferente? (Ej: Pasó de 4to a 5to)
                            if ($alumnoExistente['grado_seccion_id'] != $gradoId) {
                                if ($this->alumnoModel->actualizarGradoAlumno($alumnoExistente['id'], $gradoId)) {
                                    $updateCount++;
                                } else {
                                    $errorCount++;
                                }
                            } else {
                                // Mismo DNI, misma aula: Sin cambios, omitimos para no duplicar
                                $skipCount++;
                            }
                        } else {
                            // No existe: Registrar como nuevo
                            $insertData = [
                                'nombres' => $nombres,
                                'apellidos' => $apellidos,
                                'dni' => $dni,
                                'grado_seccion' => $gradoId
                            ];
                            if ($this->alumnoModel->registrarAlumno($insertData)) {
                                $successCount++;
                            } else {
                                $errorCount++;
                            }
                        }
                    } else {
                        $errorCount++;
                    }
                }
                fclose($tmpHandle);

                if ($successCount == 0 && $updateCount == 0 && $errorCount == 0 && $skipCount == 0 && $otherClassCount == 0) {
                    flash('alumno_message', '❌ No se detectaron datos válidos en el archivo. Revisa que las columnas coincidan con la guía.', 'alert-error');
                } else {
                    $msg = "📊 <b>Resumen del Proceso:</b><br>";
                    $msg .= "✅ <b>$successCount</b> Alumnos Nuevos registrados.<br>";
                    if ($updateCount > 0) {
                        $msg .= "🔄 <b>$updateCount</b> Alumnos Actualizados (Pasaron de Grado/Sección).<br>";
                    }
                    if ($skipCount > 0) {
                        $msg .= "ℹ️ <b>$skipCount</b> Alumnos ya registrados omitidos.<br>";
                    }
                    if ($otherClassCount > 0) {
                        $msg .= "⚠️ <b>$otherClassCount</b> Omitidos por ser de OTRAS AULAS (Solo tienes permiso para la tuya).<br>";
                    }
                    if ($errorCount > 0) {
                        $msg .= "❌ <b>$errorCount</b> Fallidos (Firma de Grado no coincide con la base de datos).";
                    }
                    flash('alumno_message', $msg);
                }
                redirect('alumnos');
            } else {
                die("Debe seleccionar un archivo");
            }
        } else {
            $data = [
                'title' => 'Importación Masiva de Alumnos'
            ];
            $this->view('alumnos/import', $data);
        }
    }

    public function imprimir($id) {
        $alumno = $this->alumnoModel->getAlumnoById($id);
        if (!$alumno) {
            die('Alumno no encontrado');
        }
        $data = [
            'alumno' => $alumno
        ];
        $this->view('alumnos/imprimir', $data);
    }
}
