<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container" style="padding-bottom: 80px;">
    <!-- Header Salutation & Clock -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem; flex-wrap: wrap; gap: 10px;">
        <div>
            <h1 style="font-weight: 700; font-size: 1.8rem; margin: 0;">¡Hola, <?php echo $_SESSION['user_name']; ?>! 👋</h1>
            <p style="color: var(--gray); margin-top: 5px;">Aquí está el resumen de hoy en tu aula.</p>
        </div>
        <div class="glass-card" style="padding: 10px 20px; display:flex; align-items:center; gap:10px; border-radius: 50px;">
            <i class="fa-regular fa-clock" style="color: var(--primary); font-size: 1.2rem;"></i>
            <span id="live-clock" style="font-weight: 600; letter-spacing: 1px;">00:00:00</span>
        </div>
    </div>

    <!-- Quick Action Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 2rem;">
        <a href="<?php echo URLROOT; ?>/scanner" class="glass-card action-btn" style="text-decoration:none; text-align:center; padding: 20px; transition: transform 0.2s;">
            <div style="width:50px; height:50px; background:rgba(99,102,241,0.2); color:var(--primary); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 10px;">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <span style="font-size: 0.9rem; font-weight: 600; color: #fff;">Escanear</span>
        </a>
        <a href="<?php echo URLROOT; ?>/alumnos/add" class="glass-card action-btn" style="text-decoration:none; text-align:center; padding: 20px; transition: transform 0.2s;">
            <div style="width:50px; height:50px; background:rgba(16,185,129,0.2); color:#10b981; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 10px;">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <span style="font-size: 0.9rem; font-weight: 600; color: #fff;">Nuevo</span>
        </a>
        <a href="<?php echo URLROOT; ?>/reportes/asistencias" class="glass-card action-btn" style="text-decoration:none; text-align:center; padding: 20px; transition: transform 0.2s;">
            <div style="width:50px; height:50px; background:rgba(245,158,11,0.2); color:#f59e0b; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin: 0 auto 10px;">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <span style="font-size: 0.9rem; font-weight: 600; color: #fff;">Reportes</span>
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid" style="margin-bottom: 2rem;">
        <div class="glass-card stat-card" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(255,255,255,0.03) 100%);">
            <div class="stat-icon" style="background: #6366f1;"><i class="fa-solid fa-users"></i></div>
            <div class="stat-val"><?php echo $data['totalAlumnos']; ?></div>
            <div class="stat-lbl">Total Alumnos</div>
        </div>
        
        <div class="glass-card stat-card" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(255,255,255,0.03) 100%);">
            <div class="stat-icon" style="background: #10b981;"><i class="fa-solid fa-check-double"></i></div>
            <div class="stat-val"><?php echo $data['totalAsistencias']; ?></div>
            <div class="stat-lbl">Asistieron Hoy</div>
        </div>

        <div class="glass-card stat-card" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(255,255,255,0.03) 100%);">
            <div class="stat-icon" style="background: #f59e0b;"><i class="fa-solid fa-door-open"></i></div>
            <div class="stat-val"><?php echo $data['permisosActivos']; ?></div>
            <div class="stat-lbl">En Permiso</div>
        </div>
    </div>

    <!-- Multi-Chart Container -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 2rem;">
        <!-- Main Chart -->
        <div class="glass-card" style="padding: 25px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 1.5rem;"><i class="fa-solid fa-chart-line" style="color: var(--primary); margin-right: 8px;"></i> Asistencia Semanal</h3>
            <div style="height: 250px; position: relative;">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart -->
        <div class="glass-card" style="padding: 25px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 1.5rem;"><i class="fa-solid fa-chart-pie" style="color: #f472b6; margin-right: 8px;"></i> Distribución de Permisos</h3>
            <div style="height: 250px; position: relative; display:flex; justify-content:center;">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="glass-card" style="padding: 25px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.1rem; margin:0;"><i class="fa-solid fa-bolt" style="color: #10b981; margin-right: 8px;"></i> Actividad Reciente</h3>
            <a href="<?php echo URLROOT; ?>/reportes/asistencias" style="color: var(--primary); font-size:0.85rem; font-weight:600; text-decoration:none;">Ver todo</a>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php if(empty($data['recientes'])): ?>
                <p style="color:var(--gray); text-align:center; padding:20px;">No hay actividad registrada hoy.</p>
            <?php else: ?>
                <?php foreach($data['recientes'] as $item): 
                    $isPermiso = (strpos($item['tipo'], 'Permiso') !== false);
                    $color = $isPermiso ? '#f59e0b' : '#10b981';
                    $bg = $isPermiso ? 'rgba(245,158,11,0.15)' : 'rgba(16,185,129,0.15)';
                    $icon = $isPermiso ? 'fa-door-open' : 'fa-user-check';
                ?>
                <div style="display: flex; align-items: center; gap: 15px; padding: 12px; background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                    <div style="width:40px; height:40px; border-radius:50%; background:<?php echo $bg; ?>; color:<?php echo $color; ?>; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">
                        <i class="fa-solid <?php echo $icon; ?>"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 0.95rem;"><?php echo htmlspecialchars($item['nombres'] . ' ' . $item['apellidos']); ?></div>
                        <div style="font-size: 0.8rem; color: var(--gray);"><?php echo $item['tipo']; ?></div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 700; font-size: 0.9rem; color: #fff;"><?php echo date('h:i A', strtotime($item['hora'])); ?></div>
                        <div style="font-size: 0.75rem; color: var(--gray);">Hoy</div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.action-btn:hover {
    transform: translateY(-5px);
    background: rgba(255,255,255,0.06) !important;
    border-color: var(--primary) !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Digital Live Clock
    setInterval(() => {
        const now = new Date();
        document.getElementById('live-clock').innerText = now.toLocaleTimeString('es-ES', {hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit'});
    }, 1000);

    // --- Weekly Chart Configuration ---
    const ctxWeekly = document.getElementById('weeklyChart').getContext('2d');
    const weeklyDataRaw = <?php echo json_encode($data['stats']); ?>;
    const wLabels = weeklyDataRaw.map(r => {
        const d = new Date(r.fecha + 'T12:00:00');
        return d.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' });
    });
    const wTotals = weeklyDataRaw.map(r => r.total);

    let wGrad = ctxWeekly.createLinearGradient(0, 0, 0, 250);
    wGrad.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
    wGrad.addColorStop(1, 'rgba(99, 102, 241, 0)');

    new Chart(ctxWeekly, {
        type: 'line',
        data: {
            labels: wLabels.length ? wLabels : ['Sin datos'],
            datasets: [{
                data: wTotals.length ? wTotals : [0],
                borderColor: '#6366f1',
                backgroundColor: wGrad,
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8', stepSize: 1 } },
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        }
    });

    // --- Donut Chart Configuration ---
    const ctxDonut = document.getElementById('donutChart').getContext('2d');
    const donutDataRaw = <?php echo json_encode($data['motivoStats']); ?>;
    
    const dLabels = donutDataRaw.map(r => r.motivo);
    const dTotals = donutDataRaw.map(r => r.total);
    
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: dLabels.length ? dLabels : ['Sin datos'],
            datasets: [{
                data: dTotals.length ? dTotals : [1],
                backgroundColor: [
                    '#6366f1', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#cbd5e1', usePointStyle: true, padding: 15 }
                }
            }
        }
    });
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
