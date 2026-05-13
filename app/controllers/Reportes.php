<?php
class Reportes extends Controller {
    protected $asistenciaModel;
    protected $permisoModel;
    protected $alumnoModel;

    public function __construct() {
        if (!isLoggedIn()) { redirect('usuarios/login'); }
        $this->asistenciaModel = $this->model('Asistencia');
        $this->permisoModel = $this->model('Permiso');
        $this->alumnoModel = $this->model('Alumno');
    }

    public function asistencias() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $gsid = $_GET['grado_seccion'] ?? '';
        
        // 🔒 RESTRICCIÓN DE ROL: Si no es Director, forzar su propio Grado
        if (!isDirector()) {
            $gsid = $_SESSION['user_grado_seccion_id'] ?? 0;
        }

        $logs = $this->asistenciaModel->getLogsByDate($fecha, $gsid);
        $grados = $this->alumnoModel->getGrades();
        
        // Filtrar los grados del selector para que al profesor solo le salga el suyo
        if (!isDirector()) {
            $grados = array_filter($grados, function($g) use ($gsid) {
                return $g['id'] == $gsid;
            });
        }

        $data = [
            'title' => 'Reporte de Asistencias',
            'fecha' => $fecha,
            'grado_seccion' => $gsid,
            'grados' => $grados,
            'registros' => $logs
        ];
        
        $this->view('reportes/asistencias', $data);
    }

    public function permisos() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $gsid = $_GET['grado_seccion'] ?? '';
        
        // 🔒 RESTRICCIÓN DE ROL: Si no es Director, forzar su propio Grado
        if (!isDirector()) {
            $gsid = $_SESSION['user_grado_seccion_id'] ?? 0;
        }

        $logs = $this->permisoModel->getLogsByDate($fecha, $gsid);
        $grados = $this->alumnoModel->getGrades();
        
        // Filtrar los grados del selector para que al profesor solo le salga el suyo
        if (!isDirector()) {
            $grados = array_filter($grados, function($g) use ($gsid) {
                return $g['id'] == $gsid;
            });
        }

        $data = [
            'title' => 'Reporte de Permisos',
            'fecha' => $fecha,
            'grado_seccion' => $gsid,
            'grados' => $grados,
            'registros' => $logs
        ];
        
        $this->view('reportes/permisos', $data);
    }

    public function resolver_permiso($id, $estado) {
        $estadoLimpio = urldecode($estado);
        $validos = ['Retornado', 'No Regresó'];
        
        if (!in_array($estadoLimpio, $validos)) {
            redirect('reportes/permisos');
        }

        if ($this->permisoModel->actualizarEstadoManual($id, $estadoLimpio)) {
            $label = ($estadoLimpio === 'No Regresó') ? '⚠️ Alumno reportado como NO REGRESADO.' : '✅ Alumno marcado como Retornado exitosamente.';
            flash('permiso_message', $label);
        } else {
            flash('permiso_message', 'Hubo un problema al actualizar el estado.', 'alert alert-danger');
        }
        redirect('reportes/permisos');
    }

    public function exportar() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $gsid = $_GET['grado_seccion'] ?? '';
        $tipo = $_GET['tipo'] ?? 'asistencia';

        // 🔒 RESTRICCIÓN DE ROL: Evitar exportaciones de otras aulas
        if (!isDirector()) {
            $gsid = $_SESSION['user_grado_seccion_id'] ?? 0;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_'.$tipo.'_'.$fecha.'.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        if ($tipo == 'permiso') {
            fputcsv($output, ['Alumno', 'Grado', 'Autorizante', 'Motivo', 'Hora Salida', 'Hora Retorno', 'Estado']);
            $logs = $this->permisoModel->getLogsByDate($fecha, $gsid);
            foreach($logs as $l) {
                fputcsv($output, [
                    $l['apellidos'] . ' ' . $l['nombres'],
                    $l['grado'] . ' ' . $l['seccion'],
                    $l['profesor'],
                    $l['motivo'],
                    $l['hora_salida'],
                    $l['hora_retorno'] ?: 'Pendiente',
                    $l['estado']
                ]);
            }
        } else {
            fputcsv($output, ['Alumno', 'DNI', 'Grado', 'Fecha', 'Hora Entrada', 'Hora Salida']);
            $logs = $this->asistenciaModel->getLogsByDate($fecha, $gsid);
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
