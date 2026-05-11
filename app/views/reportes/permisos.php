<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="glass-card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
    <form method="GET" action="<?php echo URLROOT; ?>/reportes/permisos" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
        <div style="flex:1; min-width: 150px;">
            <label>Fecha:</label>
            <input type="date" name="fecha" class="form-control" value="<?php echo $data['fecha']; ?>" style="background:#0f172a;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-outline" style="height:45px;">Filtrar</button>
            <a href="<?php echo URLROOT; ?>/reportes/exportar?tipo=permiso&fecha=<?php echo $data['fecha']; ?>" class="btn btn-primary" style="height:45px; background:#10b981;">
                <i class="fa-solid fa-file-csv"></i> Excel
            </a>
        </div>
    </form>
</div>

<div class="glass-card">
    <table>
        <thead>
            <tr>
                <th>Alumno</th>
                <th>Motivo</th>
                <th>Salida</th>
                <th>Retorno</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['registros'] as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['nombres']); ?></td>
                <td style="font-weight:600;"><?php echo $r['motivo']; ?></td>
                <td>
                    <?php echo date('h:i A', strtotime($r['hora_salida'])); ?>
                    <br>
                    <small style="color:#94a3b8;">Vence: <?php echo date('h:i A', strtotime($r['hora_estimada_retorno'])); ?></small>
                </td>
                <td><?php echo $r['hora_retorno'] ? date('h:i A', strtotime($r['hora_retorno'])) : '--:--'; ?></td>
                <td>
                    <?php 
                    if ($r['estado'] == 'Pendiente') {
                        $horaActual = date('H:i:s');
                        if ($horaActual > $r['hora_estimada_retorno']) {
                            echo '<span class="badge badge-danger" style="animation: pulse 2s infinite;"><i class="fa-solid fa-triangle-exclamation"></i> ATRASADO</span>';
                        } else {
                            echo '<span class="badge badge-warning">Fuera</span>';
                        }
                    } else {
                        echo '<span class="badge badge-success">Volvió</span>';
                    }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
