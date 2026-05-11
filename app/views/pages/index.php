<?php require APPROOT . '/views/inc/header.php'; ?>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);">
            <i class="fa-solid fa-user-graduate"></i>
        </div>
        <div class="stat-info">
            <h3>Total Alumnos</h3>
            <div class="number"><?php echo $data['totalAlumnos']; ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
            <i class="fa-solid fa-clipboard-user"></i>
        </div>
        <div class="stat-info">
            <h3>Asistencias Hoy</h3>
            <div class="number"><?php echo $data['totalAsistencias']; ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3>Permisos Activos</h3>
            <div class="number"><?php echo $data['permisosActivos']; ?></div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div class="glass-card">
        <h2 style="font-size: 1.2rem; margin-bottom: 1.5rem;">Acciones Rápidas</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <a href="<?php echo URLROOT; ?>/scanner" class="btn btn-primary" style="padding: 2rem; flex-direction: column; font-size: 1.1rem;">
                <i class="fa-solid fa-camera" style="font-size: 2.5rem; margin-bottom: 10px;"></i> Escanear QR
            </a>
            <a href="<?php echo URLROOT; ?>/alumnos/add" class="btn btn-outline" style="padding: 2rem; flex-direction: column;">
                <i class="fa-solid fa-user-plus" style="font-size: 2.5rem; margin-bottom: 10px; color: var(--secondary);"></i> Registrar
            </a>
        </div>
    </div>

    <div class="glass-card">
        <h2 style="font-size: 1.2rem; margin-bottom: 1.5rem;">Actividad Reciente</h2>
        <?php if(empty($data['recientes'])): ?>
            <p style="color: var(--gray); font-size: 0.9rem;">Sin movimientos hoy.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach($data['recientes'] as $row): ?>
                    <div style="padding-bottom: 10px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 0.9rem; font-weight: 600;"><?php echo htmlspecialchars($row['nombres']); ?></div>
                            <div style="font-size: 0.75rem; color: var(--gray);"><?php echo $row['tipo']; ?></div>
                        </div>
                        <div style="font-size: 0.8rem; font-weight: 600; color: var(--primary);">
                            <?php echo date('H:i', strtotime($row['hora'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Chart JS Analysis Container -->
<div class="glass-card" style="margin-top: 1.5rem;">
    <h2 style="font-size: 1.2rem; margin-bottom: 1.5rem;">Historial de Asistencia Semanal</h2>
    <div style="height: 300px; position: relative;">
        <canvas id="weeklyChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('weeklyChart').getContext('2d');
    
    // Parse data from PHP
    const rawData = <?php echo json_encode($data['stats']); ?>;
    
    // Format dates for display
    const labels = rawData.map(r => {
        const d = new Date(r.fecha + 'T12:00:00');
        return d.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' });
    });
    const totals = rawData.map(r => r.total);

    // Create beautiful gradient for the chart
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length > 0 ? labels : ['Sin Datos'],
            datasets: [{
                label: 'Alumnos Asistidos',
                data: totals.length > 0 ? totals : [0],
                borderColor: '#6366f1',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointRadius: 5,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#94a3b8', stepSize: 1 },
                    grid: { color: 'rgba(255,255,255,0.05)' }
                },
                x: {
                    ticks: { color: '#94a3b8' },
                    grid: { display: false }
                }
            }
        }
    });
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>

