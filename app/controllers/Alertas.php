<?php
class Alertas extends Controller {
    protected $alertaModel;

    public function __construct() {
        if(!isLoggedIn()){
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
            exit;
        }
        $this->alertaModel = $this->model('Alerta');
    }

    /* ==================================================
       Marcar todas las notificaciones como leídas (AJAX)
    ================================================== */
    public function markAll() {
        header('Content-Type: application/json');
        
        $usuario_id = $_SESSION['user_id'];
        $rol = isDirector() ? 'director' : 'profesor';

        try {
            $res = $this->alertaModel->markAllRead($usuario_id, $rol);
            echo json_encode(['status' => 'success', 'result' => $res]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}
