<?php require APPROOT . '/views/inc/header.php'; ?>

<!-- Toolbar -->
<div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.25rem; flex-wrap:wrap;">
    <div class="input-wrap" style="flex:1; max-width:340px;">
        <i class="input-icon fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre, DNI o grado...">
    </div>
    <div style="display:flex; gap:10px;">
        <a href="<?php echo URLROOT; ?>/alumnos/import" class="btn btn-accent">
            <i class="fa-solid fa-file-excel"></i> Carga Masiva
        </a>
        <a href="<?php echo URLROOT; ?>/alumnos/add" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i> Nuevo Alumno
        </a>
    </div>
</div>

<?php flash('alumno_message'); ?>

<!-- Tabla -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="title-icon" style="background:#0B0B93;"><i class="fa-solid fa-user-graduate"></i></span>
            Nómina de Alumnos
        </div>
        <span style="background:#e8edf8; color:#0B0B93; padding:4px 10px; border-radius:99px; font-size:0.75rem; font-weight:700;">
            <?php echo count($data['alumnos']); ?> matriculados
        </span>
    </div>
    <div class="table-wrap">
        <table id="alumnosTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Alumno</th>
                    <th>DNI</th>
                    <th>Aula / Grado</th>
                    <th>Estado</th>
                    <th style="text-align:right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data['alumnos'])): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:3rem; color:#9ca3af;">
                            <i class="fa-solid fa-users-slash" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                            No hay alumnos registrados en el sistema.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($data['alumnos'] as $s): ?>
                    <tr>
                        <td style="color:#9ca3af; font-size:0.75rem; font-weight:600;"><?php echo str_pad($s['id'],4,'0',STR_PAD_LEFT); ?></td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div class="avatar" style="width:34px; height:34px; background:linear-gradient(135deg,#0B0B93,#c0392b); font-size:0.72rem;">
                                    <?php echo mb_substr($s['apellidos'],0,1).mb_substr($s['nombres'],0,1); ?>
                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:0.86rem;"><?php echo htmlspecialchars($s['apellidos'].', '.$s['nombres']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:0.82rem; color:#5a6a7e;"><?php echo $s['dni']; ?></td>
                        <td>
                            <span class="badge badge-info">
                                <i class="fa-solid fa-door-open"></i>
                                <?php echo $s['grado'].' &ldquo;'.$s['seccion'].'&rdquo;'; ?>
                            </span>
                        </td>
                        <td>
                            <?php echo $s['estado']==1
                                ? '<span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:0.45rem;"></i> Activo</span>'
                                : '<span class="badge badge-danger">Inactivo</span>'; ?>
                        </td>
                        <td style="text-align:right;">
                            <a href="<?php echo URLROOT; ?>/alumnos/imprimir/<?php echo $s['id']; ?>"
                               target="_blank" class="btn btn-outline btn-sm"
                               title="Imprimir carnet QR">
                                <i class="fa-solid fa-id-card"></i> Carnet QR
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>document.addEventListener('DOMContentLoaded',()=>filterTable('searchInput','alumnosTable'));</script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
