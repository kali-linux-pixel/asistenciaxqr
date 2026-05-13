<?php require APPROOT . '/views/inc/header.php'; ?>

<!-- Filtros -->
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <form method="GET" action="<?php echo URLROOT; ?>/reportes/asistencias"
              style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
            <div style="flex:0 0 160px;">
                <label style="font-size:0.75rem;">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?php echo $data['fecha']; ?>">
            </div>
            <div style="flex:1; min-width:180px;">
                <label style="font-size:0.75rem;">Aula / Grado</label>
                <select name="grado_seccion" class="form-control" style="cursor:pointer;">
                    <?php if (isDirector()): ?>
                        <option value="">Todos los Grados</option>
                    <?php endif; ?>
                    <?php foreach($data['grados'] as $g): ?>
                        <option value="<?php echo $g['id']; ?>" <?php echo ($data['grado_seccion']==$g['id'])?'selected':''; ?>>
                            <?php echo formatAula($g['grado'], $g['seccion']); ?>
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
                    <input type="text" id="reportSearch" class="form-control" placeholder="Buscar alumno...">
                </div>
                <a href="<?php echo URLROOT; ?>/reportes/exportar?tipo=asistencia&fecha=<?php echo $data['fecha']; ?>&grado_seccion=<?php echo $data['grado_seccion']; ?>"
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
            <span class="title-icon" style="background:#0B0B93;"><i class="fa-solid fa-clipboard-list"></i></span>
            Registro de Asistencia — <?php echo date('d \d\e F \d\e Y', strtotime($data['fecha'])); ?>
        </div>
        <span class="badge badge-neutral"><?php echo count($data['registros']); ?> registros</span>
    </div>
    <div class="table-wrap">
        <table id="recordsTable">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Aula</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data['registros'])): ?>
                    <tr><td colspan="5" style="text-align:center; padding:3rem; color:#9ca3af;">
                        <i class="fa-solid fa-calendar-xmark" style="font-size:2rem; display:block; margin-bottom:10px;"></i>
                        No hay asistencias registradas para esta fecha y/o aula.
                    </td></tr>
                <?php else: ?>
                    <?php foreach($data['registros'] as $r): ?>
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:9px;">
                                <div class="avatar" style="width:30px;height:30px;background:#dbeafe;color:#1e40af;">
                                    <?php echo mb_substr($r['apellidos'],0,1); ?>
                                </div>
                                <span style="font-weight:600; font-size:0.86rem;"><?php echo htmlspecialchars($r['apellidos'].', '.$r['nombres']); ?></span>
                            </div>
                        </td>
                        <td><span class="badge badge-info"><?php echo formatAula($r['grado'], $r['seccion']); ?></span></td>
                        <td>
                            <span style="font-weight:700; color:#27ae60;">
                                <i class="fa-solid fa-right-to-bracket" style="font-size:0.75rem; margin-right:4px;"></i>
                                <?php echo date('h:i A', strtotime($r['hora_entrada'])); ?>
                            </span>
                        </td>
                        <td>
                            <?php if($r['hora_salida']): ?>
                                <span style="font-weight:700; color:#c0392b;">
                                    <i class="fa-solid fa-right-from-bracket" style="font-size:0.75rem; margin-right:4px;"></i>
                                    <?php echo date('h:i A', strtotime($r['hora_salida'])); ?>
                                </span>
                            <?php else: ?>
                                <span style="color:#9ca3af; font-size:0.8rem; font-style:italic;">Aún en el colegio</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $r['hora_salida']
                                ? '<span class="badge badge-neutral">Retirado</span>'
                                : '<span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:0.45rem;"></i> Presente</span>'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>document.addEventListener('DOMContentLoaded',()=>filterTable('reportSearch','recordsTable'));</script>
<?php require APPROOT . '/views/inc/footer.php'; ?>
