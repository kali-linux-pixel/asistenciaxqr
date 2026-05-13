<?php
class Pages extends Controller {
    protected $alumnoModel;
    protected $asistenciaModel;
    protected $permisoModel;

    public function __construct() {
        requireLogin();
        $this->alumnoModel     = $this->model('Alumno');
        $this->asistenciaModel = $this->model('Asistencia');
        $this->permisoModel    = $this->model('Permiso');
    }

    public function index() {
        $totalAlumnos = $this->alumnoModel->getCount();
        $totalAsistencias = $this->asistenciaModel->getCountToday();
        $permisosActivos = $this->permisoModel->getCountActive();
        $recientes = $this->asistenciaModel->getRecientes();
        $stats = $this->asistenciaModel->getWeeklyStats();
        $motivoStats = $this->permisoModel->getStatsByMotivo();
        $gradoStats = $this->asistenciaModel->getAttendanceByGrado();

        $data = [
            'title' => 'Panel Ejecutivo',
            'totalAlumnos' => $totalAlumnos,
            'totalAsistencias' => $totalAsistencias,
            'permisosActivos' => $permisosActivos,
            'recientes' => $recientes,
            'stats' => $stats,
            'motivoStats' => $motivoStats,
            'gradoStats' => $gradoStats
        ];

        $this->view('pages/index', $data);
    }
}
