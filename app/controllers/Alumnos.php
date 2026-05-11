<?php
class Alumnos extends Controller {
    protected $alumnoModel;

    public function __construct() {
        if (!isLoggedIn()) { redirect('usuarios/login'); }
        $this->alumnoModel = $this->model('Alumno');
    }

    public function index() {
        $alumnos = $this->alumnoModel->getAlumnos();
        $data = [
            'title' => 'Gestión de Alumnos',
            'alumnos' => $alumnos
        ];
        $this->view('alumnos/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST array
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'nombres' => trim($_POST['nombres']),
                'apellidos' => trim($_POST['apellidos']),
                'dni' => trim($_POST['dni']),
                'grado_seccion' => trim($_POST['grado_seccion']),
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
                'grades' => $grades
            ];
            $this->view('alumnos/add', $data);
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
