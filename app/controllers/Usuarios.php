<?php
class Usuarios extends Controller {

    protected $userModel;

    public function __construct() {
        // Solo cargamos userModel — los demás se cargan por método según necesidad
        $this->userModel = $this->model('Usuario');
    }

    /* --------------------------------------------------
       Método privado: obtener grades usando Alumno model
    -------------------------------------------------- */
    private function getGrades() {
        $alumnoModel = $this->model('Alumno');
        return $alumnoModel->getGrades();
    }

    /* ==================================================
       LOGIN
    ================================================== */
    public function login() {
        if (isLoggedIn()) {
            redirect(isDirector() ? 'usuarios/profesores' : 'pages/index');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawPass = $_POST['password'] ?? '';
            $_POST   = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'title'    => 'Iniciar Sesión',
                'usuario'  => trim($_POST['usuario'] ?? ''),
                'password' => trim($rawPass),
                'error'    => ''
            ];

            $user = $this->userModel->login($data['usuario'], $data['password']);

            if ($user) {
                $this->createUserSession($user);
            } else {
                $data['error'] = 'Correo/usuario o contraseña incorrectos.';
                $this->view('usuarios/login', $data);
            }
        } else {
            $this->view('usuarios/login', [
                'title'    => 'Iniciar Sesión',
                'usuario'  => '', 'password' => '', 'error' => ''
            ]);
        }
    }

    /* ==================================================
       SESSION
    ================================================== */
    public function createUserSession($user) {
        // Compatibilidad: rol 'admin' antiguo → 'director'
        $rol = $user['rol'] ?? 'profesor';
        if ($rol === 'admin' || $rol === 'administrador') $rol = 'director';

        $_SESSION['user_id']               = $user['id'];
        $_SESSION['user_name']             = $user['nombre'];
        $_SESSION['user_rol']              = $rol;
        $_SESSION['user_email']            = $user['email'] ?? '';
        $_SESSION['user_grado_seccion_id'] = $user['grado_seccion_id'] ?? null;
        redirect('pages/index');
    }

    public function logout() {
        // Destruir sesión completamente
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: ' . URLROOT . '/usuarios/login');
        exit;
    }

    /* ==================================================
       LISTADO DE PROFESORES  (Director only)
    ================================================== */
    public function profesores() {
        requireDirector();
        $data = [
            'title'      => 'Gestión de Docentes',
            'profesores' => $this->userModel->getProfesores(),
        ];
        $this->view('usuarios/profesores', $data);
    }

    /* ==================================================
       CREAR PROFESOR  (Director only)
    ================================================== */
    public function crear() {
        requireDirector();
        $grados = $this->getGrades();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawPass = $_POST['password'] ?? '';
            $_POST   = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'title'            => 'Nuevo Docente',
                'grados'           => $grados,
                'nombre'           => trim($_POST['nombre']           ?? ''),
                'email'            => trim($_POST['email']            ?? ''),
                'grado_seccion_id' => trim($_POST['grado_seccion']    ?? ''),
                'password'         => trim($rawPass),
                'error_nombre'     => '',
                'error_email'      => '',
                'error_grado'      => '',
                'error_pass'       => '',
            ];

            $ok = true;
            if (empty($data['nombre'])) {
                $data['error_nombre'] = 'El nombre es obligatorio.'; $ok = false;
            }
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['error_email'] = 'Ingrese un correo válido.'; $ok = false;
            } elseif ($this->userModel->emailExists($data['email'])) {
                $data['error_email'] = 'Este correo ya está registrado.'; $ok = false;
            }
            if (empty($data['grado_seccion_id'])) {
                $data['error_grado'] = 'Seleccione un aula.'; $ok = false;
            }
            if (strlen($data['password']) < 6) {
                $data['error_pass'] = 'La contraseña debe tener al menos 6 caracteres.'; $ok = false;
            }

            if ($ok) {
                // Generar usuario a partir del email (parte antes del @)
                $data['usuario'] = strtolower(explode('@', $data['email'])[0]);

                if ($this->userModel->createProfesor($data)) {
                    flash('prof_msg', 'Docente '.$data['nombre'].' registrado exitosamente.', 'alert alert-success');
                    redirect('usuarios/profesores');
                } else {
                    $data['error_nombre'] = 'Error al guardar. Intente nuevamente.';
                    $this->view('usuarios/crear', $data);
                }
            } else {
                $this->view('usuarios/crear', $data);
            }

        } else {
            $this->view('usuarios/crear', [
                'title'            => 'Nuevo Docente',
                'grados'           => $grados,
                'nombre'           => '',
                'email'            => '',
                'grado_seccion_id' => '',
                'password'         => '',
                'error_nombre'     => '',
                'error_email'      => '',
                'error_grado'      => '',
                'error_pass'       => '',
            ]);
        }
    }

    /* ==================================================
       EDITAR PROFESOR  (Director only)
    ================================================== */
    public function editar($id = null) {
        requireDirector();
        if (!$id) redirect('usuarios/profesores');

        $profesor = $this->userModel->getById($id);
        if (!$profesor || $profesor['rol'] !== 'profesor') redirect('usuarios/profesores');

        $grados = $this->getGrades();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawPass = $_POST['password'] ?? '';
            $_POST   = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'title'            => 'Editar Docente',
                'grados'           => $grados,
                'id'               => $id,
                'nombre'           => trim($_POST['nombre']        ?? ''),
                'email'            => trim($_POST['email']         ?? ''),
                'grado_seccion_id' => trim($_POST['grado_seccion'] ?? ''),
                'password'         => trim($rawPass),
                'error_nombre'     => '',
                'error_email'      => '',
                'error_grado'      => '',
                'error_pass'       => '',
            ];

            $ok = true;
            if (empty($data['nombre'])) {
                $data['error_nombre'] = 'El nombre es obligatorio.'; $ok = false;
            }
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['error_email'] = 'Ingrese un correo válido.'; $ok = false;
            } elseif ($this->userModel->emailExists($data['email'], $id)) {
                $data['error_email'] = 'Este correo ya está en uso.'; $ok = false;
            }
            if (empty($data['grado_seccion_id'])) {
                $data['error_grado'] = 'Seleccione un aula.'; $ok = false;
            }
            if (!empty($data['password']) && strlen($data['password']) < 6) {
                $data['error_pass'] = 'La contraseña debe tener al menos 6 caracteres.'; $ok = false;
            }

            if ($ok) {
                $this->userModel->updateProfesor($id, $data);
                flash('prof_msg', 'Datos del docente actualizados.', 'alert alert-success');
                redirect('usuarios/profesores');
            } else {
                $this->view('usuarios/editar', $data);
            }

        } else {
            $this->view('usuarios/editar', [
                'title'            => 'Editar Docente',
                'grados'           => $grados,
                'id'               => $id,
                'nombre'           => $profesor['nombre'],
                'email'            => $profesor['email'] ?? '',
                'grado_seccion_id' => $profesor['grado_seccion_id'] ?? '',
                'password'         => '',
                'error_nombre'     => '',
                'error_email'      => '',
                'error_grado'      => '',
                'error_pass'       => '',
            ]);
        }
    }

    /* ==================================================
       ELIMINAR PROFESOR  (Director only)
    ================================================== */
    public function eliminar($id = null) {
        requireDirector();
        if (!$id) redirect('usuarios/profesores');
        $this->userModel->deleteProfesor($id);
        flash('prof_msg', 'Docente eliminado del sistema.', 'alert alert-success');
        redirect('usuarios/profesores');
    }

    /* ==================================================
       MANTENER SESIÓN ACTIVA (AJAX Heartbeat)
    ================================================== */
    public function keepAlive() {
        header('Content-Type: application/json');
        // Re-inicializar sesión o simplemente imprimir status
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }
        echo json_encode(['status' => 'alive', 'time' => time()]);
        exit;
    }
}
