<?php
class Mensajes extends Controller {
    private $mensajeModel;
    private $usuarioModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('usuarios/login');
        }
        $this->mensajeModel = $this->model('Mensaje');
        $this->usuarioModel = $this->model('Usuario'); // Por si necesitamos datos del docente
    }

    /** Panel Inicial */
    public function index() {
        $userId = $_SESSION['user_id'];
        $userRol = $_SESSION['user_rol'];

        if ($userRol === 'profesor') {
            // --- VISTA DEL DOCENTE ---
            // Los profesores solo tienen canal de chat DIRECTO con el Director principal
            $director = $this->mensajeModel->getDirector();
            if (!$director) {
                die("Error: No se encontró una cuenta de Dirección configurada en el sistema.");
            }

            // Marcar como leídos los que el Director le mandó al profesor
            $this->mensajeModel->marcarLeidos($director['id'], $userId);

            $conversacion = $this->mensajeModel->getConversacion($userId, $director['id']);

            $data = [
                'title' => 'Buzón: Consultas a Dirección',
                'destinatario' => $director,
                'conversacion' => $conversacion
            ];

            $this->view('mensajes/chat_profesor', $data);

        } else {
            // --- VISTA DEL DIRECTOR ---
            // El Director ve una bandeja de entrada con todos los profesores y alertas de no leídos
            $bandeja = $this->mensajeModel->getProfesoresResumen($userId);

            $data = [
                'title' => 'Bandeja de Mensajes',
                'profesores' => $bandeja
            ];

            $this->view('mensajes/bandeja_director', $data);
        }
    }

    /** Vista del Chat de un Profesor específico para el Director */
    public function chat($profesorId) {
        if (!isDirector()) {
            redirect('pages/index');
        }

        $directorId = $_SESSION['user_id'];

        // Buscar los datos del profesor con el que hablará
        $db = new Database; // Forma rápida local para cargar info de ese profesor
        $db->query("SELECT id, nombre, usuario FROM usuarios WHERE id = :id AND rol = 'profesor' LIMIT 1");
        $db->bind(':id', $profesorId);
        $profesor = $db->single();

        if (!$profesor) {
            flash('msg_bandeja', 'El docente no existe o fue removido.', 'alert alert-danger');
            redirect('mensajes/index');
        }

        // Marcar como leídos los mensajes que ese profesor le mandó al Director
        $this->mensajeModel->marcarLeidos($profesorId, $directorId);

        $conversacion = $this->mensajeModel->getConversacion($directorId, $profesorId);

        $data = [
            'title' => 'Chat con ' . $profesor['nombre'],
            'destinatario' => $profesor,
            'conversacion' => $conversacion
        ];

        $this->view('mensajes/chat_director', $data);
    }

    /** Procesar el envío del formulario de mensaje */
    public function enviar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $remitente_id = $_SESSION['user_id'];
            $destinatario_id = filter_input(INPUT_POST, 'destinatario_id', FILTER_VALIDATE_INT);
            $contenido = trim(filter_input(INPUT_POST, 'contenido', FILTER_SANITIZE_SPECIAL_CHARS));

            if (!$destinatario_id || empty($contenido)) {
                // No enviar vacío
                redirect($_SERVER['HTTP_REFERER'] ?? 'mensajes/index');
            }

            $this->mensajeModel->enviar($remitente_id, $destinatario_id, $contenido);

            // Redireccionar a donde venía el usuario para simular flujo en vivo
            redirect($_SERVER['HTTP_REFERER'] ?? 'mensajes/index');
        } else {
            redirect('mensajes/index');
        }
    }
}
