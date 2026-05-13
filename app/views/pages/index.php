<?php require APPROOT . '/views/inc/header.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.dash-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem; }
@media(max-width:960px){ .dash-grid { grid-template-columns: 1fr; } }

.quick-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.quick-btn {
    background: #fff;
    border: 1.5px solid #d5dbe6;
    border-radius: 12px;
    padding: 1.1rem 0.75rem;
    text-align: center;
    cursor: pointer;
    transition: all .22s;
    text-decoration: none;
    display: block;
}
.quick-btn:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(11,11,147,0.1); border-color: #0B0B93; }
.quick-btn .qb-icon {
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; color: #fff;
    margin: 0 auto 8px;
}
.quick-btn .qb-label { font-size: 0.78rem; font-weight: 700; color: #374151; }
.quick-btn .qb-sub   { font-size: 0.68rem; color: #9ca3af; margin-top: 2px; }

.feed-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f0f2f5;
}
.feed-item:last-child { border-bottom: none; }
.feed-avatar {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
</style>

<!-- Accesos Rápidos Inteligentes -->
<div class="section-title" style="margin-bottom:0.75rem;">
    <i class="fa-solid fa-bolt" style="color:#fbbd08;"></i> Accesos Rápidos
</div>
<div class="quick-grid">
    <?php if(isDirector()): ?>
        <!-- ACCESOS DIRECTOR -->
        <a href="<?php echo URLROOT; ?>/notas" class="quick-btn" style="border-color:#f39c12;">
            <div class="qb-icon" style="background:#f39c12; box-shadow: 0 4px 10px rgba(243,156,18,0.25);"><i class="fa-solid fa-star"></i></div>
            <div class="qb-label" style="color:#f39c12;">Monitoreo Notas</div>
            <div class="qb-sub">Revisar Avance</div>
        </a>
        <a href="<?php echo URLROOT; ?>/usuarios/profesores" class="quick-btn" style="border-color:#0B0B93;">
            <div class="qb-icon" style="background:#0B0B93; box-shadow: 0 4px 10px rgba(11,11,147,0.2);"><i class="fa-solid fa-chalkboard-user"></i></div>
            <div class="qb-label">Docentes</div>
            <div class="qb-sub">Gestionar Cuentas</div>
        </a>
        <a href="<?php echo URLROOT; ?>/alumnos/import" class="quick-btn" style="border-color:#27ae60;">
            <div class="qb-icon" style="background:#27ae60; box-shadow: 0 4px 10px rgba(39,174,96,0.2);"><i class="fa-solid fa-file-import"></i></div>
            <div class="qb-label">Carga Masiva</div>
            <div class="qb-sub">Importar Excel</div>
        </a>
        <a href="<?php echo URLROOT; ?>/reportes/asistencias" class="quick-btn">
            <div class="qb-icon" style="background:#c0392b;"><i class="fa-solid fa-clipboard-list"></i></div>
            <div class="qb-label">Asistencias</div>
            <div class="qb-sub">Reporte Global</div>
        </a>
    <?php else: ?>
        <!-- ACCESOS PROFESOR -->
        <a href="<?php echo URLROOT; ?>/notas" class="quick-btn" style="border-color:#f39c12;">
            <div class="qb-icon" style="background:#f39c12; box-shadow: 0 4px 10px rgba(243,156,18,0.25);"><i class="fa-solid fa-star"></i></div>
            <div class="qb-label" style="color:#f39c12;">Calificar Notas</div>
            <div class="qb-sub">Subir Registro</div>
        </a>
        <a href="<?php echo URLROOT; ?>/scanner" class="quick-btn" style="border-color:#0B0B93;">
            <div class="qb-icon" style="background:#0B0B93; box-shadow: 0 4px 10px rgba(11,11,147,0.2);"><i class="fa-solid fa-camera"></i></div>
            <div class="qb-label">Escáner QR</div>
            <div class="qb-sub">Tomar Asistencia</div>
        </a>
        <a href="<?php echo URLROOT; ?>/alumnos" class="quick-btn">
            <div class="qb-icon" style="background:#27ae60;"><i class="fa-solid fa-user-graduate"></i></div>
            <div class="qb-label">Mis Alumnos</div>
            <div class="qb-sub">Ver Lista</div>
        </a>
        <a href="<?php echo URLROOT; ?>/reportes/permisos" class="quick-btn">
            <div class="qb-icon" style="background:#8e44ad;"><i class="fa-solid fa-ticket"></i></div>
            <div class="qb-label">Papeletas</div>
            <div class="qb-sub">Permisos</div>
        </a>
    <?php endif; ?>
</div>

<!-- Estadísticas del Día -->
<div class="section-title">
    <i class="fa-solid fa-chart-bar" style="color:#0B0B93;"></i> Resumen del Día
</div>
<div class="stats-grid" style="margin-bottom:1.5rem;">
    <div class="stat-card" style="border-left: 4px solid #0B0B93;">
        <div class="stat-icon-wrap" style="background:#0B0B93;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-info">
            <div class="stat-val"><?php echo $data['totalAlumnos']; ?></div>
            <div class="stat-lbl">Alumnos Matriculados</div>
            <div class="stat-sub"><i class="fa-solid fa-check-circle"></i> Total en el sistema</div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #27ae60;">
        <div class="stat-icon-wrap" style="background:#27ae60;">
            <i class="fa-solid fa-check-double"></i>
        </div>
        <div class="stat-info">
            <div class="stat-val"><?php echo $data['totalAsistencias']; ?></div>
            <div class="stat-lbl">Asistencias Hoy</div>
            <div class="stat-sub" style="color:#27ae60;"><i class="fa-solid fa-calendar-day"></i> <?php echo date('d/m/Y'); ?></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #f39c12;">
        <div class="stat-icon-wrap" style="background:#f39c12;">
            <i class="fa-solid fa-person-walking-arrow-right"></i>
        </div>
        <div class="stat-info">
            <div class="stat-val"><?php echo $data['permisosActivos']; ?></div>
            <div class="stat-lbl">Alumnos con Permiso</div>
            <div class="stat-sub" style="color:#f39c12;"><i class="fa-solid fa-hourglass-half"></i> Fuera del aula ahora</div>
        </div>
    </div>
</div>

<!-- Gráficas + Feed -->
<div class="dash-grid">
    <!-- Gráfica Semanal -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span class="title-icon" style="background:#0B0B93;"><i class="fa-solid fa-chart-line"></i></span>
                Asistencia Semanal
            </div>
            <span style="font-size:0.72rem; color:#9ca3af;">Últimos 7 días</span>
        </div>
        <div class="card-body">
            <canvas id="weeklyChart" style="max-height:210px;"></canvas>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span class="title-icon" style="background:#c0392b;"><i class="fa-solid fa-clock-rotate-left"></i></span>
                Actividad Reciente
            </div>
            <a href="<?php echo URLROOT; ?>/reportes/asistencias" style="font-size:0.75rem; color:#0B0B93; font-weight:700;">Ver todo</a>
        </div>
        <div class="card-body" style="padding:0.75rem 1.25rem;">
            <?php if(empty($data['recientes'])): ?>
                <div style="text-align:center; padding:2rem; color:#9ca3af;">
                    <i class="fa-solid fa-inbox" style="font-size:1.8rem; display:block; margin-bottom:8px;"></i>
                    Sin actividad hoy
                </div>
            <?php else: ?>
                <?php foreach($data['recientes'] as $item):
                    $isPermiso = strpos($item['tipo'], 'Permiso') !== false;
                    $iconBg    = $isPermiso ? '#fff0cc' : '#e8f8f0';
                    $iconColor = $isPermiso ? '#f39c12' : '#27ae60';
                    $icon      = $isPermiso ? 'fa-door-open' : 'fa-user-check';
                ?>
                <div class="feed-item">
                    <div class="feed-avatar" style="background:<?php echo $iconBg; ?>; color:<?php echo $iconColor; ?>;">
                        <i class="fa-solid <?php echo $icon; ?>"></i>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-weight:700; font-size:0.82rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            <?php echo htmlspecialchars($item['apellidos'].' '.$item['nombres']); ?>
                        </div>
                        <div style="font-size:0.72rem; color:#9ca3af;"><?php echo $item['tipo']; ?></div>
                    </div>
                    <div style="font-size:0.78rem; font-weight:700; color:#374151; flex-shrink:0;">
                        <?php echo date('h:i A', strtotime($item['hora'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Comparativa por Aula e Información -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span class="title-icon" style="background:#f39c12;"><i class="fa-solid fa-chart-pie"></i></span>
                Motivos de Salida
            </div>
        </div>
        <div class="card-body">
            <div style="position:relative; height:220px;">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <span class="title-icon" style="background:#27ae60;"><i class="fa-solid fa-school"></i></span>
                Asistencia por Aula (Hoy)
            </div>
        </div>
        <div class="card-body">
            <div style="position:relative; height:220px;">
                <canvas id="gradoBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfica de asistencia semanal
const wRaw = <?php echo json_encode($data['stats']); ?>;
const wLabels = wRaw.map(r => {
    const d = new Date(r.fecha + 'T12:00:00');
    return d.toLocaleDateString('es-PE', { weekday:'short', day:'numeric' });
});
const ctx = document.getElementById('weeklyChart').getContext('2d');
const grad = ctx.createLinearGradient(0,0,0,210);
grad.addColorStop(0, 'rgba(11,11,147,0.18)');
grad.addColorStop(1, 'rgba(11,11,147,0)');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: wLabels.length ? wLabels : ['Sin datos'],
        datasets: [{
            data: wRaw.length ? wRaw.map(r=>r.total) : [0],
            backgroundColor: wLabels.map((_, i) =>
                i === wLabels.length-1 ? '#0B0B93' : 'rgba(11,11,147,0.25)'),
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: '#f0f2f5' }, ticks: { color: '#9ca3af', stepSize: 1 }, beginAtZero: true },
            x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
        }
    }
});

// Gráfica dona de motivos
const dRaw = <?php echo json_encode($data['motivoStats']); ?>;
new Chart('donutChart', {
    type: 'doughnut',
    data: {
        labels: dRaw.length ? dRaw.map(r => r.motivo) : ['Sin datos'],
        datasets: [{
            data: dRaw.length ? dRaw.map(r => r.total) : [1],
            backgroundColor: ['#0B0B93','#c0392b','#f39c12','#27ae60','#8e44ad'],
            borderWidth: 2, borderColor: '#fff', hoverOffset: 8
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '60%',
        plugins: {
            legend: {
                position: 'right',
                labels: { color: '#5a6a7e', usePointStyle: true, padding: 15, font: { size: 11, weight: 'bold' } }
            }
        }
    }
});

// NUEVA Gráfica Comparativa por Aula
const gRaw = <?php echo json_encode($data['gradoStats']); ?>;
new Chart('gradoBarChart', {
    type: 'bar',
    data: {
        labels: gRaw.length ? gRaw.map(r => r.label) : ['Sin aulas'],
        datasets: [{
            data: gRaw.length ? gRaw.map(r => r.total) : [0],
            backgroundColor: '#27ae60',
            borderRadius: 5,
            maxBarThickness: 30
        }]
    },
    options: {
        indexAxis: 'y', // Barras horizontales para mejor lectura
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: '#f0f2f5' }, ticks: { precision: 0, color: '#94a3b8' } },
            y: { grid: { display: false }, ticks: { color: '#475569', font: { weight: 'bold' } } }
        }
    }
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
