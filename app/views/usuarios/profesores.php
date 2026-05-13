<?php require APPROOT . '/views/inc/header.php'; ?>

<?php flash('prof_msg'); ?>

<!-- Encabezado de sección -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
    <div>
        <div style="font-size:0.78rem; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:3px;">
            <i class="fa-solid fa-chalkboard-user" style="color:#0B0B93; margin-right:5px;"></i>Panel de Dirección
        </div>
        <p style="font-size:0.85rem; color:#5a6a7e;">Gestiona las cuentas de los docentes y sus aulas asignadas.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/usuarios/crear" class="btn btn-primary">
        <i class="fa-solid fa-user-plus"></i> Nuevo Docente
    </a>
</div>

<!-- Resumen estadístico -->
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem;">
    <div class="card" style="border-left:4px solid #0B0B93; padding:1.1rem 1.25rem; display:flex; align-items:center; gap:14px;">
        <div style="width:44px;height:44px;background:#e8edf8;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#0B0B93;font-size:1.1rem;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div style="font-size:1.8rem;font-weight:800;color:#1a2332;line-height:1;"><?php echo count($data['profesores']); ?></div>
            <div style="font-size:0.75rem;color:#5a6a7e;font-weight:600;text-transform:uppercase;">Docentes</div>
        </div>
    </div>
    <div class="card" style="border-left:4px solid #27ae60; padding:1.1rem 1.25rem; display:flex; align-items:center; gap:14px;">
        <div style="width:44px;height:44px;background:#e8f8f0;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#27ae60;font-size:1.1rem;">
            <i class="fa-solid fa-door-open"></i>
        </div>
        <div>
            <div style="font-size:1.8rem;font-weight:800;color:#1a2332;line-height:1;">
                <?php echo count(array_unique(array_filter(array_column($data['profesores'],'grado_seccion_id')))); ?>
            </div>
            <div style="font-size:0.75rem;color:#5a6a7e;font-weight:600;text-transform:uppercase;">Aulas Asignadas</div>
        </div>
    </div>
</div>

<!-- Tabla de docentes -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="title-icon" style="background:#0B0B93;"><i class="fa-solid fa-list"></i></span>
            Nómina de Docentes
        </div>
        <div class="input-wrap" style="width:240px;">
            <i class="input-icon fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchProf" class="form-control" placeholder="Buscar docente...">
        </div>
    </div>
    <div class="table-wrap">
        <table id="profTable">
            <thead>
                <tr>
                    <th>Docente</th>
                    <th>Correo Institucional</th>
                    <th>Aula Asignada</th>
                    <th style="text-align:right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data['profesores'])): ?>
                    <tr><td colspan="4" style="text-align:center;padding:3rem;color:#9ca3af;">
                        <i class="fa-solid fa-user-slash" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                        No hay docentes registrados. Use el botón "Nuevo Docente" para agregar uno.
                    </td></tr>
                <?php else: ?>
                    <?php foreach($data['profesores'] as $p): ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="avatar" style="width:36px;height:36px;background:linear-gradient(135deg,#0B0B93,#1a3a8f);font-size:0.78rem;">
                                    <?php echo mb_strtoupper(mb_substr($p['nombre'],0,2)); ?>
                                </div>
                                <div>
                                    <div style="font-weight:700;font-size:0.88rem;"><?php echo htmlspecialchars($p['nombre']); ?></div>
                                    <div style="font-size:0.72rem;color:#9ca3af;">@<?php echo $p['usuario']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="mailto:<?php echo $p['email']; ?>" style="color:#0B0B93;font-weight:600;font-size:0.85rem;">
                                <i class="fa-solid fa-envelope" style="margin-right:5px;"></i>
                                <?php echo htmlspecialchars($p['email']); ?>
                            </a>
                        </td>
                        <td>
                            <?php if($p['grado']): ?>
                                <span class="badge badge-info">
                                    <i class="fa-solid fa-door-open"></i>
                                    <?php echo formatAula($p['grado'], $p['seccion']); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-neutral">Sin asignar</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right;">
                            <a href="<?php echo URLROOT; ?>/usuarios/editar/<?php echo $p['id']; ?>"
                               class="btn btn-outline btn-sm" style="margin-right:5px;">
                                <i class="fa-solid fa-pen"></i> Editar
                            </a>
                            <a href="<?php echo URLROOT; ?>/usuarios/eliminar/<?php echo $p['id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Eliminar al docente <?php echo addslashes($p['nombre']); ?>? Esta acción no se puede deshacer.');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>document.addEventListener('DOMContentLoaded',()=>filterTable('searchProf','profTable'));</script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
