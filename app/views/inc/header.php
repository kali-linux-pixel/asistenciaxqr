<?php if(!class_exists('Alerta')) require_once APPROOT . '/models/Alerta.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITENAME; ?> — <?php echo isset($title) ? $title : (isset($data['title']) ? $data['title'] : 'Panel'); ?></title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        #nprogress { position:fixed; top:0; left:0; right:0; height:3px; z-index:9999; background:linear-gradient(to right,#c0392b,#f39c12); width:0%; transition:width .4s ease; box-shadow:0 0 6px rgba(243,156,18,0.6); }
        .rol-badge-director { background:#1a1a9e; color:#fff; padding:3px 9px; border-radius:99px; font-size:0.65rem; font-weight:800; letter-spacing:0.5px; text-transform:uppercase; }
        .rol-badge-profesor  { background:#27ae60; color:#fff; padding:3px 9px; border-radius:99px; font-size:0.65rem; font-weight:800; letter-spacing:0.5px; text-transform:uppercase; }

        /* === NOTIFICACIONES === */
        .notif-btn { position:relative; background:#f1f5f9; border:1.5px solid #e2e8f0; border-radius:10px; width:38px; height:38px; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .2s; color:#475569; }
        .notif-btn:hover { background:#e8edf8; border-color:#0B0B93; color:#0B0B93; transform:scale(1.05); }
        .notif-badge { position:absolute; top:-6px; right:-6px; background:#c0392b; color:#fff; font-size:0.6rem; font-weight:900; width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid #fff; animation: pulse-badge 2s infinite; }
        @keyframes pulse-badge { 0%,100%{box-shadow:0 0 0 0 rgba(192,57,43,0.4)} 50%{box-shadow:0 0 0 6px rgba(192,57,43,0)} }
        .notif-dropdown { position:absolute; top:calc(100% + 10px); right:0; width:340px; background:#fff; border-radius:14px; box-shadow:0 20px 50px rgba(0,0,0,0.15); border:1px solid #e2e8f0; z-index:999; display:none; overflow:hidden; animation: slideDown .2s ease; }
        @keyframes slideDown { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
        .notif-dropdown.open { display:block; }
        .notif-header { padding:12px 16px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; }
        .notif-header span { font-weight:800; font-size:0.85rem; color:#0f172a; }
        .notif-header a { font-size:0.72rem; color:#0B0B93; font-weight:600; cursor:pointer; }
        .notif-item { padding:10px 16px; border-bottom:1px solid #f1f5f9; display:flex; gap:10px; align-items:flex-start; transition:background .15s; cursor:pointer; }
        .notif-item:hover { background:#f8fafc; }
        .notif-item.unread { background:#eff6ff; border-left:3px solid #0B0B93; }
        .notif-icon { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
        .notif-body { flex:1; }
        .notif-title { font-size:0.78rem; font-weight:700; color:#0f172a; }
        .notif-msg { font-size:0.71rem; color:#64748b; margin-top:2px; line-height:1.4; }
        .notif-time { font-size:0.65rem; color:#94a3b8; margin-top:3px; }
        .notif-empty { padding:30px; text-align:center; color:#94a3b8; font-size:0.82rem; }

        /* === SESSION TIMEOUT MODAL === */
        #session-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
        #session-modal.show { display:flex; }
        .session-box { background:#fff; border-radius:20px; padding:2.5rem; text-align:center; max-width:380px; box-shadow:0 30px 80px rgba(0,0,0,0.25); animation:slideDown .3s ease; }
        .session-box h3 { color:#c0392b; font-size:1.3rem; font-weight:900; margin-bottom:8px; }
        .session-box p { color:#64748b; font-size:0.88rem; margin-bottom:20px; }
        #session-countdown { font-size:2.5rem; font-weight:900; color:#c0392b; }

        /* === MICRO-ANIMATIONS === */
        .btn { position:relative; overflow:hidden; }
        .btn::after { content:''; position:absolute; inset:0; background:rgba(255,255,255,0.25); border-radius:inherit; opacity:0; transition:opacity .3s; }
        .btn:active::after { opacity:1; }
        .card { animation: fadeInUp .35s ease both; }
        @keyframes fadeInUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .stat-card { transition: transform .22s, box-shadow .22s; }
        .stat-card:hover { transform:translateY(-4px); box-shadow:0 12px 30px rgba(11,11,147,0.12); }
        .nav-link { transition: all .18s ease; }
        tr { transition: background .15s; }

    </style>
</head>
<body>
<div id="nprogress"></div>

<?php if(isLoggedIn()): ?>
<div class="app-layout">

    <!-- ========== SIDEBAR ========== -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-stripe"></div>
            <div class="logo-wrap">
                <div class="logo-img-box">
                    <img src="<?php echo URLROOT; ?>/img/logo.ui.png" alt="Logo">
                </div>
                <div class="logo-text">
                    <div class="school-name"><?php echo SITENAME; ?></div>
                    <div class="school-type">Sistema Escolar</div>
                </div>
            </div>
        </div>

        <?php if(isDirector()): ?>
        <!-- MENÚ DIRECTOR -->
        <div class="nav-section-label">Dirección</div>
        <ul class="nav-menu">
            <li>
                <a href="<?php echo URLROOT; ?>/pages/index" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span>
                    Panel Principal
                </a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/usuarios/profesores" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-chalkboard-user"></i></span>
                    Gestión de Docentes
                </a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/reportes/asistencias" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span>
                    Asistencias Globales
                </a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/reportes/permisos" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-ticket"></i></span>
                    Permisos Globales
                </a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/alumnos" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-user-graduate"></i></span>
                    Alumnos
                </a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/notas" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-star"></i></span>
                    Registro de Notas
                </a>
            </li>
        </ul>

        <?php else: ?>
        <!-- MENÚ PROFESOR -->
        <div class="nav-section-label">Gestión de Aula</div>
        <ul class="nav-menu">
            <li>
                <a href="<?php echo URLROOT; ?>/pages/index" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/scanner" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-camera"></i></span>
                    Escáner QR
                </a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/notas" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-star"></i></span>
                    Calificar Notas
                </a>
            </li>
        </ul>
        <div class="nav-section-label">Mi Aula</div>
        <ul class="nav-menu">
            <li>
                <a href="<?php echo URLROOT; ?>/alumnos" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-user-graduate"></i></span>
                    Mis Alumnos
                </a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/reportes/asistencias" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span>
                    Asistencias
                </a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/reportes/permisos" class="nav-link">
                    <span class="nav-icon"><i class="fa-solid fa-ticket"></i></span>
                    Papeletas
                </a>
            </li>
        </ul>
        <?php endif; ?>

        <!-- Panel del usuario -->
        <div class="user-panel">
            <div class="user-card">
                <div class="user-avatar">
                    <?php echo mb_strtoupper(mb_substr($_SESSION['user_name'], 0, 2)); ?>
                </div>
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                    <div class="user-role" style="margin-top:3px;">
                        <?php if(isDirector()): ?>
                            <span class="rol-badge-director"><i class="fa-solid fa-star"></i> Director</span>
                        <?php else: ?>
                            <span class="rol-badge-profesor"><i class="fa-solid fa-chalkboard"></i> Docente</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <a href="<?php echo URLROOT; ?>/usuarios/logout" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
            </a>
        </div>
    </aside>

    <!-- ========== CONTENIDO ========== -->
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <div class="topbar-title"><?php echo isset($title) ? $title : (isset($data['title']) ? $data['title'] : 'Panel'); ?></div>
                <div class="topbar-breadcrumb">
                    <i class="fa-solid fa-house"></i>
                    <i class="fa-solid fa-angle-right"></i>
                    <span><?php echo isset($title) ? $title : (isset($data['title']) ? $data['title'] : 'Inicio'); ?></span>
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date">
                    <i class="fa-regular fa-calendar"></i>
                    <?php echo date('d/m/Y'); ?>
                </div>
                <div class="topbar-date" style="background:#fff0cc; color:#8a5d00;">
                    <i class="fa-regular fa-clock"></i>
                    <span id="clock-time">--:--</span>
                </div>



                <!-- CAMPANA DE NOTIFICACIONES -->
                <?php
                    $unread = 0; $recientes = [];
                    try {
                        $alertaModel = new Alerta();
                        $unread = $alertaModel->countUnread($_SESSION['user_id'], isDirector() ? 'director' : 'profesor');
                        $recientes = $alertaModel->getRecent($_SESSION['user_id'], isDirector() ? 'director' : 'profesor', 6);
                        if(isDirector()) {
                            $alertaModel->generarAlertasRiesgo();
                            $alertaModel->generarAlertasNotasPendientes();
                            $unread = $alertaModel->countUnread($_SESSION['user_id'], 'director');
                            $recientes = $alertaModel->getRecent($_SESSION['user_id'], 'director', 6);
                        }
                    } catch (Exception $e) { /* Tabla no creada aún — silencioso */ }
                ?>
                <div style="position:relative;">
                    <div class="notif-btn" id="notifBtn" onclick="toggleNotif()">
                        <i class="fa-solid fa-bell"></i>
                        <?php if($unread > 0): ?>
                            <span class="notif-badge"><?php echo $unread > 9 ? '9+' : $unread; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <span>🔔 Notificaciones <?php if($unread > 0): ?><span style="color:#c0392b;">(<?php echo $unread; ?>)</span><?php endif; ?></span>
                            <a onclick="markAllRead()">Marcar todo leído</a>
                        </div>
                        <?php if(empty($recientes)): ?>
                            <div class="notif-empty">
                                <i class="fa-regular fa-bell-slash" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                                Sin notificaciones nuevas
                            </div>
                        <?php else: ?>
                            <?php foreach($recientes as $notif):
                                $iconos = ['riesgo_asistencia'=>['⚠️','#fef3c7'], 'notas_pendientes'=>['📝','#eff6ff'], 'papeleta'=>['🎫','#f0fdf4']];
                                $ic = $iconos[$notif['tipo']] ?? ['🔔','#f8fafc'];
                            ?>
                                <div class="notif-item <?php echo !$notif['leida'] ? 'unread' : ''; ?>" 
                                     onclick="window.location='<?php echo $notif['link'] ?: '#'; ?>'">
                                    <div class="notif-icon" style="background:<?php echo $ic[1]; ?>; font-size:1.1rem;"><?php echo $ic[0]; ?></div>
                                    <div class="notif-body">
                                        <div class="notif-title"><?php echo htmlspecialchars($notif['titulo']); ?></div>
                                        <div class="notif-msg"><?php echo htmlspecialchars(mb_substr($notif['mensaje'], 0, 90)) . '...'; ?></div>
                                        <div class="notif-time"><i class="fa-regular fa-clock"></i> <?php echo date('d/m H:i', strtotime($notif['created_at'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-body">

<!-- SESSION TIMEOUT MODAL -->
<div id="session-modal">
    <div class="session-box">
        <div style="font-size:3rem; margin-bottom:10px;">⏳</div>
        <h3>Sesión por Expirar</h3>
        <p>Tu sesión cerrará automáticamente en:</p>
        <div id="session-countdown">5:00</div>
        <div style="margin-top:20px; display:flex; gap:10px; justify-content:center;">
            <button onclick="keepSession()" class="btn btn-primary">✅ Mantener Sesión</button>
            <a href="<?php echo URLROOT; ?>/usuarios/logout" class="btn btn-outline">Cerrar Sesión</a>
        </div>
    </div>
</div>

<script>
// === RELOJ LIVE ===
(function clock(){ 
    const t = new Date(); 
    let h = t.getHours();
    const m = t.getMinutes().toString().padStart(2,'0');
    const s = t.getSeconds().toString().padStart(2,'0');
    const ampm = h >= 12 ? 'p. m.' : 'a. m.';
    h = h % 12;
    h = h ? h : 12; // el 0 se vuelve 12
    const hourStr = h.toString().padStart(2,'0');
    
    const el = document.getElementById('clock-time');
    if(el) {
        el.textContent = `${hourStr}:${m}:${s} ${ampm}`;
    }
    setTimeout(clock, 1000);
})();

// === BARRA DE CARGA SUPERIOR ===
setTimeout(() => { const n = document.getElementById('nprogress'); if(n){ n.style.width='80%'; setTimeout(()=>{ n.style.width='100%'; setTimeout(()=>n.style.display='none',300); }, 400); }}, 100);

// === NOTIFICACIONES DROPDOWN ===
function toggleNotif() {
    const d = document.getElementById('notifDropdown');
    d.classList.toggle('open');
}
document.addEventListener('click', function(e) {
    if(!e.target.closest('#notifBtn') && !e.target.closest('#notifDropdown')) {
        document.getElementById('notifDropdown').classList.remove('open');
    }
});
function markAllRead() {
    fetch('<?php echo URLROOT; ?>/alertas/markAll', {method:'POST'})
        .then(() => { document.getElementById('notifDropdown').innerHTML = '<div class="notif-empty">✅ Todo marcado como leído</div>'; })
        .catch(() => {});
    // Quitar badge visual
    const b = document.querySelector('.notif-badge');
    if(b) b.remove();
    document.getElementById('notifDropdown').classList.remove('open');
}



// === SESSION TIMEOUT (25 min idle → warning, 30 min → logout) ===
let idleTime = 0;
const WARNING_AT = 25 * 60; // segundos
const LOGOUT_AT  = 30 * 60;
let countdownInterval;

function resetIdle() { idleTime = 0; }
['mousemove','keydown','click','scroll','touchstart'].forEach(e => document.addEventListener(e, resetIdle));

setInterval(() => {
    idleTime++;
    if(idleTime === WARNING_AT) {
        document.getElementById('session-modal').classList.add('show');
        let secs = (LOGOUT_AT - WARNING_AT);
        updateCountdown(secs);
        countdownInterval = setInterval(() => {
            secs--;
            updateCountdown(secs);
            if(secs <= 0) { window.location = '<?php echo URLROOT; ?>/usuarios/logout'; }
        }, 1000);
    }
}, 1000);

function updateCountdown(s) {
    const m = Math.floor(s/60), sec = s % 60;
    document.getElementById('session-countdown').textContent = m + ':' + sec.toString().padStart(2,'0');
}
function keepSession() {
    resetIdle();
    clearInterval(countdownInterval);
    document.getElementById('session-modal').classList.remove('show');
    fetch('<?php echo URLROOT; ?>/usuarios/keepAlive').catch(()=>{});
}
</script>
<?php endif; ?>
