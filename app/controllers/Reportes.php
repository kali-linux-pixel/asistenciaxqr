<?php
class Reportes extends Controller {
    protected $asistenciaModel;
    protected $permisoModel;

    public function __construct() {
        if (!isLoggedIn()) { redirect('usuarios/login'); }
        $this->asistenciaModel = $this->model('Asistencia');
        $this->permisoModel = $this->model('Permiso');
    }

    public function asistencias() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $logs = $this->asistenciaModel->getLogsByDate($fecha);
        
        $data = [
            'title' => 'Reporte de Asistencias',
            'fecha' => $fecha,
            'registros' => $logs
        ];
        
        $this->view('reportes/asistencias', $data);
    }

    public function permisos() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $logs = $this->permisoModel->getLogsByDate($fecha);
        
        $data = [
            'title' => 'Reporte de Permisos',
            'fecha' => $fecha,
            'registros' => $logs
        ];
        
        $this->view('reportes/permisos', $data);
    }

    public function exportar() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $tipo = $_GET['tipo'] ?? 'asistencia';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_'.$tipo.'_'.$fecha.'.csv');
        
        $output = fopen('php://output', 'w');
        
        // BOM to make Excel read UTF8 correctly
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if ($tipo == 'permiso') {
            fputcsv($output, ['Alumno', 'Grado', 'Motivo', 'Hora Salida', 'Hora Retorno', 'Estado']);
            $logs = $this->permisoModel->getLogsByDate($fecha);
            foreach($logs as $l) {
                fputcsv($output, [
                    $l['apellidos'] . ' ' . $l['nombres'],
                    $l['grado'] . ' ' . $l['seccion'],
                    $l['motivo'],
                    $l['hora_salida'],
                    $l['hora_retorno'] ?: 'Pendiente',
                    $l['estado']
                ]);
            }
        } else {
            fputcsv($output, ['Alumno', 'DNI', 'Grado', 'Fecha', 'Hora Entrada', 'Hora Salida']);
            $logs = $this->asistenciaModel->getLogsByDate($fecha);
            foreach($logs as $l) {
                fputcsv($output, [
                    $l['apellidos'] . ' ' . $l['nombres'],
                    $l['dni'],
                    $l['grado'] . ' ' . $l['seccion'],
                    $l['fecha'],
                    $l['hora_entrada'],
                    $l['hora_salida'] ?: 'En Colegio'
                ]);
            }
        }
        fclose($output);
        exit;
    }
}
