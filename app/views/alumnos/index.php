<?php require APPROOT . '/views/inc/header.php'; ?>

<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <?php flash('alumno_message'); ?>
    <a href="<?php echo URLROOT; ?>/alumnos/add" class="btn btn-primary">
        <i class="fa-solid fa-user-plus"></i> Nuevo Alumno
    </a>
</div>

<div class="glass-card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Alumno</th>
                <th>DNI</th>
                <th>Grado</th>
                <th>Estado</th>
                <th style="text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['alumnos'] as $s): ?>
                <tr>
                    <td>#<?php echo $s['id']; ?></td>
                    <td style="font-weight: 600;"><?php echo htmlspecialchars($s['apellidos'] . ', ' . $s['nombres']); ?></td>
                    <td><?php echo $s['dni']; ?></td>
                    <td><?php echo $s['grado'] . ' ' . $s['seccion']; ?></td>
                    <td>
                        <?php echo ($s['estado'] == 1) ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>'; ?>
                    </td>
                    <td style="text-align: right;">
                        <a href="<?php echo URLROOT; ?>/alumnos/imprimir/<?php echo $s['id']; ?>" target="_blank" class="btn btn-outline" style="padding: 6px 12px;">
                            <i class="fa-solid fa-print"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
