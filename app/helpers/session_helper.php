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
        die('<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>403 - ACCESO DENEGADO</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
            <style>
                body {
                    margin: 0; padding: 0;
                    background: #05050c;
                    color: #ff003c;
                    font-family: "Rajdhani", sans-serif;
                    height: 100vh;
                    display: flex; align-items: center; justify-content: center;
                    overflow: hidden;
                    position: relative;
                }
                body::before {
                    content: "";
                    position: absolute; top: 0; left: 0; width: 200%; height: 200%;
                    background-image: 
                        linear-gradient(0deg, transparent 24%, rgba(255, 0, 60, 0.05) 25%, rgba(255, 0, 60, 0.05) 26%, transparent 27%, transparent 74%, rgba(255, 0, 60, 0.05) 75%, rgba(255, 0, 60, 0.05) 76%, transparent 77%, transparent),
                        linear-gradient(90deg, transparent 24%, rgba(255, 0, 60, 0.05) 25%, rgba(255, 0, 60, 0.05) 26%, transparent 27%, transparent 74%, rgba(255, 0, 60, 0.05) 75%, rgba(255, 0, 60, 0.05) 76%, transparent 77%, transparent);
                    background-size: 50px 50px;
                    transform: perspective(500px) rotateX(60deg) translateY(-150px);
                    animation: grid-move 25s linear infinite;
                    z-index: 1;
                }
                @keyframes grid-move {
                    0% { background-position: 0 0; }
                    100% { background-position: 0 1000px; }
                }
                .container {
                    background: rgba(5, 5, 12, 0.92);
                    border: 2.5px solid #ff003c;
                    box-shadow: 0 0 35px rgba(255, 0, 60, 0.4), inset 0 0 20px rgba(255, 0, 60, 0.25);
                    border-radius: 10px;
                    padding: 3.5rem 3rem;
                    text-align: center;
                    z-index: 10;
                    position: relative;
                    width: 90%;
                    max-width: 480px;
                    backdrop-filter: blur(12px);
                    box-sizing: border-box;
                }
                .container::before {
                    content: "ALERTA DE SEGURIDAD QR";
                    position: absolute; top: -14px; left: 50%; transform: translateX(-50%);
                    background: #ff003c; color: #fff;
                    font-family: "Orbitron", sans-serif; font-weight: 900; font-size: 0.7rem;
                    padding: 4px 16px; border-radius: 4px; letter-spacing: 2px;
                    box-shadow: 0 0 15px #ff003c;
                }
                .shield-icon {
                    font-size: 4.5rem;
                    color: #ff003c;
                    margin-bottom: 1.25rem;
                    animation: pulse-glow 1.5s infinite ease-in-out;
                }
                @keyframes pulse-glow {
                    0%, 100% { transform: scale(1); filter: drop-shadow(0 0 12px #ff003c); opacity: 1; }
                    50% { transform: scale(1.05); filter: drop-shadow(0 0 25px #ff003c); opacity: 0.85; }
                }
                h1 {
                    font-family: "Orbitron", sans-serif;
                    font-size: 2.2rem; font-weight: 900;
                    margin: 0 0 12px 0;
                    letter-spacing: 4px;
                    color: #fff;
                    text-shadow: 0 0 8px #ff003c, 0 0 15px #ff003c;
                }
                p {
                    font-size: 1rem;
                    line-height: 1.6;
                    color: #94a3b8;
                    margin-bottom: 2.25rem;
                    letter-spacing: 1.2px;
                    text-transform: uppercase;
                }
                .highlight {
                    color: #FBBF24;
                    font-weight: 700;
                    text-shadow: 0 0 8px rgba(251,191,36,0.6);
                }
                .btn-return {
                    display: inline-flex; align-items: center; justify-content: center; gap: 10px;
                    background: transparent;
                    border: 2px solid #ff003c;
                    color: #ff003c;
                    padding: 14px 28px;
                    font-family: "Orbitron", sans-serif;
                    font-weight: 800; text-transform: uppercase;
                    border-radius: 5px;
                    text-decoration: none;
                    letter-spacing: 2px;
                    font-size: 0.8rem;
                    transition: all 0.3s ease;
                    box-shadow: 0 0 10px rgba(255, 0, 60, 0.15);
                    width: 100%;
                    box-sizing: border-box;
                }
                .btn-return:hover {
                    background: #ff003c;
                    color: #fff;
                    box-shadow: 0 0 25px #ff003c;
                    transform: translateY(-3px);
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="shield-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h1>ACCESO DENEGADO</h1>
                <p>Firma de encriptación inválida.<br>Sólo el Rango <span class="highlight">DIRECTOR</span> cuenta con credenciales de Nivel 5 habilitadas.</p>
                <a href="'.URLROOT.'/pages/index" class="btn-return">
                    <i class="fa-solid fa-power-off"></i> ABORTAR OPERACIÓN
                </a>
            </div>
        </body>
        </html>');
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

/** Formatea Grado y Sección limpiamente a formato 5to "A" */
function formatAula($grado, $seccion) {
    $gradoLimpio = trim(str_replace(['Grado', 'grado', 'Año', 'año'], '', $grado));
    $seccLimpia = trim(str_replace(['"', "'", '“', '”', '«', '»'], '', $seccion));
    return $gradoLimpio . ' "' . $seccLimpia . '"';
}
