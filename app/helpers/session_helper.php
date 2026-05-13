<?php
session_start();

/* --------------------------------------------------------
   FLASH MESSAGE
-------------------------------------------------------- */
function flash($name = '', $message = '', $class = 'alert alert-success') {
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            $_SESSION[$name]          = $message;
            $_SESSION[$name.'_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class = $_SESSION[$name.'_class'] ?? '';
            echo '<div class="'.$class.'" style="margin-bottom:1rem;">'.$_SESSION[$name].'</div>';
            unset($_SESSION[$name], $_SESSION[$name.'_class']);
        }
    }
}

/* --------------------------------------------------------
   AUTH HELPERS
-------------------------------------------------------- */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isDirector() {
    return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'director';
}

function isProfesor() {
    return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'profesor';
}

/** Aborta con 403 si el usuario no es Director */
function requireDirector() {
    if (!isLoggedIn() || !isDirector()) {
        http_response_code(403);
        die('<div style="font-family:sans-serif;padding:3rem;text-align:center;">
            <h2>Acceso Denegado</h2>
            <p>Solo el Director puede acceder a esta sección.</p>
            <a href="'.URLROOT.'/pages/index">Volver al inicio</a>
        </div>');
    }
}

/** Aborta si no está autenticado */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('usuarios/login');
        exit;
    }
}

function redirect($page) {
    header('Location: '.URLROOT.'/'.$page);
    exit;
}
