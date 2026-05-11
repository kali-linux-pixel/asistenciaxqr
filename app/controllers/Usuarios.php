<?php
class Usuarios extends Controller {
    protected $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('Usuario');
    }

    public function login() {
        // Check if already logged in
        if (isLoggedIn()) {
            redirect('pages/index');
        }

        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // We sanitize the array but grab raw password to avoid mutating special chars before verify
            $rawPassword = $_POST['password'] ?? ''; 
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'usuario' => trim($_POST['usuario'] ?? ''),
                'password' => trim($rawPassword),
                'error' => ''
            ];

            // Check user
            $loggedInUser = $this->userModel->login($data['usuario'], $data['password']);

            if ($loggedInUser) {
                // Create Session
                $this->createUserSession($loggedInUser);
            } else {
                $data['error'] = 'Credenciales incorrectas.';
                $this->view('usuarios/login', $data);
            }

        } else {
            // Init data
            $data = [
                'usuario' => '',
                'password' => '',
                'error' => ''
            ];

            // Load view
            $this->view('usuarios/login', $data);
        }
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_rol'] = $user['rol'];
        redirect('pages/index');
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_rol']);
        session_destroy();
        redirect('usuarios/login');
    }
}
