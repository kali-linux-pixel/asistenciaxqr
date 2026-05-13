<?php require APPROOT . '/views/inc/header.php'; ?>

<!-- Filtros -->
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <form method="GET" action="<?php echo URLROOT; ?>/reportes/permisos"
              style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
            <div style="flex:0 0 160px;">
                <label style="font-size:0.75rem;">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?php echo $data['fecha']; ?>">
            </div>
            <div style="flex:1; min-width:180px;">
                <label style="font-size:0.75rem;">Aula / Grado</label>
                <select name="grado_seccion" class="form-control" style="cursor:pointer;">
                    <option value="">Todos los Grados</option>
                    <?php foreach($data['grados'] as $g): ?>
                        <option value="<?php echo $g['id']; ?>" <?php echo ($data['grado_seccion']==$g['id'])?'selected':''; ?>>
                            <?php echo $g['grado'].' — '.$g['seccion']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
            <div style="margin-left:auto; display:flex; gap:8px; align-items:flex-end;">
                <div class="input-wrap" style="width:220px;">
                    <i class="input-icon fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="permisoSearch" class="form-control" placeholder="Buscar alumno...">
                </div>
                <a href="<?php echo URLROOT; ?>/reportes/exportar?tipo=permiso&fecha=<?php echo $data['fecha']; ?>&grado_seccion=<?php echo $data['grado_seccion']; ?>"
                   class="btn btn-accent">
                    <i class="fa-solid fa-file-excel"></i> Exportar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="title-icon" style="background:#f39c12;"><i class="fa-solid fa-ticket"></i></span>
            Papeletas de Permiso — <?php echo date('d \d\e F \d\e Y', strtotime($data['fecha'])); ?>
        </div>
        <span class="badge badge-neutral"><?php echo count($data['registros']); ?> papeletas</span>
    </div>
    <div class="table-wrap">
        <table id="permisosTable">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Motivo</th>
                    <th>Docente Autoriza</th>
                    <th>Salida</th>
                    <th>Retorno</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data['registros'])): ?>
                    <tr><td colspan="6" style="text-align:center; padding:3rem; color:#9ca3af;">
                        <i class="fa-solid fa-ticket" style="font-size:2rem; display:block; margin-bottom:10px; opacity:0.3;"></i>
                        No se registraron papeletas de permiso esta fecha.
                    </td></tr>
                <?php else: ?>
                    <?php foreach($data['registros'] as $r):
                        $hora = date('H:i:s');
                        $atrasado = ($r['estado']==='Pendiente' && isset($r['hora_estimada_retorno']) && $hora > $r['hora_estimada_retorno']);
                    ?>
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:9px;">
                                <div class="avatar" style="width:30px;height:30px;background:#fdecea;color:#c0392b;">
                                    <?php echo mb_substr($r['apellidos'],0,1); ?>
                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:0.86rem;"><?php echo htmlspecialchars($r['apellidos'].', '.$r['nombres']); ?></div>
                                    <?php if(isset($r['grado'])): ?>
                                    <div style="font-size:0.72rem; color:#9ca3af;"><?php echo $r['grado'].' '.$r['seccion']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-warning">
                                <?php echo htmlspecialchars($r['motivo']); ?>
                            </span>
                        </td>
                        <td style="font-size:0.82rem; color:#5a6a7e;">
                            <i class="fa-solid fa-chalkboard-user" style="color:#0B0B93; margin-right:5px;"></i>
                            <?php echo htmlspecialchars($r['profesor'] ?? 'No registrado'); ?>
                        </td>
                        <td>
                            <div style="font-weight:700; color:#c0392b; font-size:0.85rem;"><?php echo date('h:i A', strtotime($r['hora_salida'])); ?></div>
                            <?php if(isset($r['hora_estimada_retorno'])): ?>
                            <div style="font-size:0.7rem; color:#9ca3af;">Máx: <?php echo date('h:i A', strtotime($r['hora_estimada_retorno'])); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($r['hora_retorno']): ?>
                                <span style="font-weight:700; color:#27ae60; font-size:0.85rem;">
                                    <i class="fa-solid fa-check" style="margin-right:3px;"></i>
                                    <?php echo date('h:i A', strtotime($r['hora_retorno'])); ?>
                                </span>
                            <?php else: ?>
                                <span style="color:#9ca3af; font-style:italic; font-size:0.8rem;">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($r['estado']==='Retornado'): ?>
                                <span class="badge badge-success"><i class="fa-solid fa-check-circle"></i> Retornó</span>
                            <?php elseif($atrasado): ?>
                                <span class="badge badge-danger" style="animation:pulse 1.5s infinite;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Tardanza
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning"><i class="fa-solid fa-hourglass-half"></i> Fuera</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>document.addEventListener('DOMContentLoaded',()=>filterTable('permisoSearch','permisosTable'));</script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
