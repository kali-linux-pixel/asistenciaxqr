<?php
class Scanner extends Controller {
    protected $alumnoModel;
    protected $asistenciaModel;
    protected $permisoModel;

    public function __construct() {
        if (!isLoggedIn()) { redirect('usuarios/login'); }
        $this->alumnoModel = $this->model('Alumno');
        $this->asistenciaModel = $this->model('Asistencia');
        $this->permisoModel = $this->model('Permiso');
    }

    public function index() {
        $data = [
            'title' => 'Escáner de QR y Control'
        ];
        $this->view('scanner/index', $data);
    }

    public function process() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            die(json_encode(['status' => 'error', 'message' => 'Solo POST']));
        }

        header('Content-Type: application/json');
        $token  = $_POST['token'] ?? '';
        $dni    = $_POST['dni'] ?? '';
        $mode   = $_POST['mode'] ?? '';
        $motivo = $_POST['motivo'] ?? 'Otro';
        $tiempo = intval($_POST['tiempo'] ?? 10); // Added duration capture
        $profesor_id = $_SESSION['user_id'];

        if (empty($token) && empty($dni)) {
            echo json_encode(['status' => 'error', 'message' => 'No se recibió QR ni DNI']);
            exit;
        }

        $alumno = null;
        if (!empty($token)) {
            $alumno = $this->alumnoModel->getAlumnoByToken($token);
        } elseif (!empty($dni)) {
            $alumno = $this->alumnoModel->getAlumnoByDni($dni);
        }

        if (!$alumno) {
            echo json_encode(['status' => 'error', 'message' => 'ALUMNO NO REGISTRADO']);
            exit;
        }

        $alumnoNombre = htmlspecialchars($alumno['apellidos'] . ', ' . $alumno['nombres']);
        $alumnoId = $alumno['id'];
        date_default_timezone_set('America/Lima');

        try {
            if ($mode === 'asistencia') {
                $registro = $this->asistenciaModel->checkTodayAttendance($alumnoId);

                if (!$registro) {
                    // Entrar
                    $this->asistenciaModel->registrarEntrada($alumnoId);
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'ENTRADA REGISTRADA',
                        'alumno' => $alumnoNombre,
                        'timestamp' => 'Hora: ' . date('h:i A')
                    ]);
                } else {
                    if (is_null($registro['hora_salida'])) {
                        // Salir
                        $this->asistenciaModel->registrarSalida($registro['id']);
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'SALIDA REGISTRADA',
                            'alumno' => $alumnoNombre,
                            'timestamp' => 'Hora: ' . date('h:i A')
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Ya registró entrada y salida hoy.',
                            'alumno' => $alumnoNombre
                        ]);
                    }
                }
            } 
            elseif ($mode === 'permiso_salida') {
                $prm = $this->permisoModel->hasPending($alumnoId);
                if ($prm) {
                     echo json_encode(['status' => 'error', 'message' => 'Ya tiene permiso pendiente.', 'alumno' => $alumnoNombre]);
                     exit;
                }

                // Calcular hora estimada de retorno o Permanente
                if ($tiempo >= 999) {
                    $estimatedTime = '23:59:59'; // Fin de día
                    $msgVence = 'PERMANENTE';
                } else {
                    $estimatedTime = date('H:i:s', strtotime("+$tiempo minutes"));
                    $msgVence = date('h:i A', strtotime($estimatedTime));
                }

                $this->permisoModel->registrarSalida($alumnoId, $profesor_id, $motivo, $estimatedTime);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'SALIDA AUTORIZADA: ' . $motivo,
                    'alumno' => $alumnoNombre,
                    'timestamp' => date('h:i A') . ' (Vence: ' . $msgVence . ')'
                ]);
            } 
            elseif ($mode === 'permiso_retorno') {
                $prm = $this->permisoModel->hasPending($alumnoId);
                if ($prm) {
                    $this->permisoModel->registrarRetorno($prm['id']);
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'RETORNO AL AULA CONFIRMADO',
                        'alumno' => $alumnoNombre,
                        'timestamp' => date('h:i A')
                    ]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'No hay permisos pendientes.', 'alumno' => $alumnoNombre]);
                }
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Err: ' . $e->getMessage()]);
        }
    }
}
