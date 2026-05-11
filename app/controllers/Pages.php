<?php
class Pages extends Controller {
    protected $alumnoModel;
    protected $asistenciaModel;
    protected $permisoModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('usuarios/login');
        }
        $this->alumnoModel = $this->model('Alumno');
        $this->asistenciaModel = $this->model('Asistencia');
        $this->permisoModel = $this->model('Permiso');
    }

    public function index() {
        $totalAlumnos = $this->alumnoModel->getCount();
        $totalAsistencias = $this->asistenciaModel->getCountToday();
        $permisosActivos = $this->permisoModel->getCountActive();
        $recientes = $this->asistenciaModel->getRecientes();
        $stats = $this->asistenciaModel->getWeeklyStats();
        $motivoStats = $this->permisoModel->getStatsByMotivo();

        $data = [
            'title' => 'Panel de Control',
            'totalAlumnos' => $totalAlumnos,
            'totalAsistencias' => $totalAsistencias,
            'permisosActivos' => $permisosActivos,
            'recientes' => $recientes,
            'stats' => $stats,
            'motivoStats' => $motivoStats
        ];

        $this->view('pages/index', $data);
    }
}
