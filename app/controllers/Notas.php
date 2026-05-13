<?php
class Notas extends Controller {
    protected $notaModel;
    protected $cursoModel;
    protected $alumnoModel;
    protected $db;

    public function __construct() {
        requireLogin();
        $this->notaModel = $this->model('Nota');
        $this->cursoModel = $this->model('Curso');
        $this->alumnoModel = $this->model('Alumno');
    }

    public function index() {
        // Si es profesor, restringir a su aula. Si es director, mostrar combo de aulas.
        $isDir = isDirector();
        $grades = $this->alumnoModel->getGrades();
        $cursos = $this->cursoModel->getCursos();

        $selectedGrado = null;
        $selectedCurso = null;
        $alumnosNotas = [];

        // SI ES PROFESOR: Cargar automáticamente su grado de la sesión
        $teacherGradeName = '';
        if (!$isDir) {
            $selectedGrado = $_SESSION['user_grado_seccion_id'] ?? null;
            if($selectedGrado) {
                // Buscar nombre del grado para mostrarlo
                $gInfo = $this->alumnoModel->getGradeById($selectedGrado);
                if($gInfo) {
                    $teacherGradeName = $gInfo['grado'] . ' "' . $gInfo['seccion'] . '"';
                }
            }
        } else {
            // Si es director, lee el que seleccionó del dropdown
            $selectedGrado = $_GET['grado'] ?? null;
        }

        $selectedCurso = $_GET['curso'] ?? null;
        $profesorEncargado = 'No asignado';
        $reporteCumplimiento = [];
        $periodo = $_GET['periodo'] ?? '1er Bimestre';

        if ($isDir) {
            // Cargar reporte global para la vista principal del director
            $reporteCumplimiento = $this->notaModel->getComplianceReport($periodo);
        }

        if ($selectedGrado && $selectedCurso) {
            $alumnosNotas = $this->notaModel->getNotasByCursoYGrado($selectedCurso, $selectedGrado);
            
            // Buscar nombre del profesor asignado a esta aula
            $this->db = new Database(); // Instanciar para consulta rápida
            $this->db->query("SELECT nombre FROM usuarios WHERE grado_seccion_id = :gsid AND rol = 'profesor' LIMIT 1");
            $this->db->bind(':gsid', $selectedGrado);
            $prof = $this->db->single();
            if ($prof) {
                $profesorEncargado = $prof['nombre'];
            }
        }

        // Validar si están vacías para lanzar alerta de subir notas
        $faltanNotas = false;
        if (!empty($alumnosNotas)) {
            $completas = 0;
            foreach($alumnosNotas as $an) { if(!empty($an['nota'])) $completas++; }
            if($completas == 0) $faltanNotas = true;
        }

        $data = [
            'title' => 'Control de Calificaciones',
            'isDirector' => $isDir,
            'grades' => $grades,
            'cursos' => $cursos,
            'selectedGrado' => $selectedGrado,
            'selectedCurso' => $selectedCurso,
            'alumnosNotas' => $alumnosNotas,
            'periodo' => $periodo,
            'faltanNotas' => $faltanNotas,
            'teacherGradeName' => $teacherGradeName,
            'profesorEncargado' => $profesorEncargado,
            'reporteCumplimiento' => $reporteCumplimiento
        ];

        $this->view('notas/index', $data);
    }

    public function guardar() {
        if (isDirector()) {
            flash('nota_message', '⛔ El Director solo puede VER notas. Los Profesores son quienes las ingresan.', 'alert-error');
            redirect('notas');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $curso_id = $_POST['curso_id'];
            $periodo  = $_POST['periodo'];
            $grades_data = $_POST['notas']; // Array indexed by student_id => value
            $comments_data = $_POST['comentarios']; // Array indexed by student_id => comment

            $success = true;
            foreach ($grades_data as $alumno_id => $valor) {
                if ($valor === '') continue; // Ignorar vacíos

                $res = $this->notaModel->registrarNota([
                    'alumno_id' => $alumno_id,
                    'curso_id' => $curso_id,
                    'periodo' => $periodo,
                    'nota' => $valor,
                    'comentario' => $comments_data[$alumno_id] ?? ''
                ]);
                if (!$res) $success = false;
            }

            if ($success) {
                flash('nota_message', '✅ Registro de calificaciones guardado correctamente.');
            } else {
                flash('nota_message', '⚠️ Algunas notas no se pudieron guardar.', 'alert-error');
            }
            
            // Volver con los mismos filtros
            $redUrl = 'notas?curso=' . $curso_id . '&periodo=' . urlencode($periodo) . '&grado=' . $_POST['grado_id'];
            redirect($redUrl);
        }
    }

    public function exportar() {
        if (!isDirector()) {
            die("Acceso no autorizado");
        }

        $grado_id = $_GET['grado'] ?? null;
        $curso_id = $_GET['curso'] ?? null;
        $periodo  = $_GET['periodo'] ?? '1er Bimestre';

        if (!$grado_id || !$curso_id) {
            die("Faltan parámetros");
        }

        $alumnosNotas = $this->notaModel->getNotasByCursoYGrado($curso_id, $grado_id);
        
        // Cargar metadata
        $gInfo = $this->alumnoModel->getGradeById($grado_id);
        $aulaName = $gInfo ? ($gInfo['grado'] . ' ' . $gInfo['seccion']) : 'Sin_Aula';
        
        $cData = $this->cursoModel->getCursoById($curso_id);
        $cursoName = $cData ? $cData['nombre'] : 'Materia';

        // Forzar descarga de Excel
        $fileName = "Calificaciones_" . preg_replace('/[^A-Za-z0-9\-]/', '_', $aulaName) . "_" . date('Y-m-d') . ".xls";
        
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Soporte para tildes/acentos en Excel
        echo "\xEF\xBB\xBF"; 
        ?>
        <table border="1">
            <thead>
                <tr style="background:#0B0B93; color:#ffffff;">
                    <th colspan="4" style="font-size:16px; text-align:center;">REGISTRO OFICIAL DE CALIFICACIONES</th>
                </tr>
                <tr>
                    <th><b>Aula:</b></th>
                    <th><?php echo htmlspecialchars($aulaName); ?></th>
                    <th><b>Periodo:</b></th>
                    <th><?php echo htmlspecialchars($periodo); ?></th>
                </tr>
                <tr>
                    <th><b>Curso:</b></th>
                    <th colspan="3"><?php echo htmlspecialchars($cursoName); ?></th>
                </tr>
                <tr style="background:#cccccc; font-weight:bold;">
                    <th style="width:30px;">Nº</th>
                    <th style="width:300px;">Apellidos y Nombres</th>
                    <th style="width:80px; text-align:center;">Nota</th>
                    <th style="width:250px;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $c = 1; foreach($alumnosNotas as $a): ?>
                    <tr>
                        <td><?php echo $c++; ?></td>
                        <td><?php echo htmlspecialchars($a['apellidos'] . ', ' . $a['nombres']); ?></td>
                        <td align="center" style="font-weight:bold;"><?php echo htmlspecialchars($a['nota'] ?? '--'); ?></td>
                        <td><?php echo htmlspecialchars($a['comentario'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        exit();
    }
}
