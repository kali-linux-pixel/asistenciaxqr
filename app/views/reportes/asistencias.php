<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="glass-card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
    <form method="GET" action="<?php echo URLROOT; ?>/reportes/asistencias" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
        <div style="flex:1; min-width: 150px;">
            <label>Fecha:</label>
            <input type="date" name="fecha" class="form-control" value="<?php echo $data['fecha']; ?>" style="background:#0f172a;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-outline" style="height:45px;">Filtrar</button>
            <a href="<?php echo URLROOT; ?>/reportes/exportar?tipo=asistencia&fecha=<?php echo $data['fecha']; ?>" class="btn btn-primary" style="height:45px; background:#10b981;">
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
                <th>Grado</th>
                <th>Entrada</th>
                <th>Salida</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['registros'] as $r): ?>
            <tr>
                <td style="font-weight:600;"><?php echo htmlspecialchars($r['apellidos'] . ', ' . $r['nombres']); ?></td>
                <td><?php echo $r['grado'] . ' ' . $r['seccion']; ?></td>
                <td style="color:#34d399;"><?php echo date('h:i A', strtotime($r['hora_entrada'])); ?></td>
                <td><?php echo $r['hora_salida'] ? date('h:i A', strtotime($r['hora_salida'])) : '--:--'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
