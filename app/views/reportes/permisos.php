<?php require APPROOT . '/views/inc/header.php'; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Filter Control Box -->
    <div class="glass-card" style="padding: 1.5rem; display:flex; align-items:center;">
        <form method="GET" action="<?php echo URLROOT; ?>/reportes/permisos" style="width:100%; display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
            <div style="flex:1; min-width:140px;">
                <label style="margin-bottom: 3px; font-size:0.8rem;">Fecha:</label>
                <input type="date" name="fecha" class="form-control" value="<?php echo $data['fecha']; ?>" style="background:rgba(0,0,0,0.2); border-radius:8px; padding:10px;">
            </div>
            <div style="flex:1; min-width:180px;">
                <label style="margin-bottom: 3px; font-size:0.8rem;">Grado/Sección:</label>
                <select name="grado_seccion" class="form-control" style="background:rgba(0,0,0,0.2); border-radius:8px; padding:10px; color:#fff;">
                    <option value="">-- Todos los Grados --</option>
                    <?php foreach($data['grados'] as $g): ?>
                        <option value="<?php echo $g['id']; ?>" <?php echo ($data['grado_seccion'] == $g['id']) ? 'selected' : ''; ?>>
                            <?php echo $g['grado'] . ' ' . $g['seccion']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="height:45px; border-radius:8px; background:var(--secondary);">
                <i class="fa-solid fa-sync-alt"></i> Filtrar
            </button>
        </form>
    </div>

    <!-- Live Search & Export Panel -->
    <div class="glass-card" style="padding: 1.5rem; display:flex; align-items:center; gap: 12px;">
        <div style="flex:1; position:relative;">
            <i class="fa-solid fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--gray);"></i>
            <input type="text" id="permisoSearch" placeholder="Búsqueda rápida..." style="width:100%; padding:12px 12px 12px 35px; border-radius:8px; background:rgba(255,255,255,0.02); border:1px solid var(--glass-border); color:#fff; outline:none;">
        </div>
        <a href="<?php echo URLROOT; ?>/reportes/exportar?tipo=permiso&fecha=<?php echo $data['fecha']; ?>&grado_seccion=<?php echo $data['grado_seccion']; ?>" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); height:45px; border-radius:8px; font-weight:700;">
            <i class="fa-solid fa-file-excel"></i> Exportar
        </a>
    </div>
</div>

<div class="glass-card" style="padding:0; overflow:hidden; border-radius:15px;">
    <div style="padding:1.5rem; border-bottom:1px solid var(--glass-border); background:rgba(255,255,255,0.01);">
        <h3 style="margin:0; font-size:1rem;"><i class="fa-solid fa-user-shield" style="color:var(--warning); margin-right:8px;"></i> Listado de Papeletas y Movimiento</h3>
    </div>
    <div style="overflow-x: auto;">
        <table id="permisosTable" style="margin:0;">
            <thead>
                <tr style="background: rgba(0,0,0,0.1);">
                    <th style="padding-left:1.5rem;">Estudiante</th>
                    <th>Razón / Motivo</th>
                    <th>Cronología Salida</th>
                    <th>Hora Retorno</th>
                    <th style="padding-right:1.5rem;">Situación</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data['registros'])): ?>
                    <tr><td colspan="5" style="text-align:center; color:var(--gray); padding:40px;">No se registraron permisos el día seleccionado.</td></tr>
                <?php else: ?>
                    <?php foreach($data['registros'] as $r): ?>
                    <tr style="transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                        <td style="padding-left:1.5rem; font-weight:600; color:#fff;"><?php echo htmlspecialchars($r['nombres']); ?></td>
                        <td>
                            <span style="background:rgba(245,158,11,0.1); color:var(--warning); padding:4px 10px; border-radius:6px; font-weight:600; font-size:0.85rem;">
                                <?php echo $r['motivo']; ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-size:0.9rem; color:#fff; font-weight:500;"><?php echo date('h:i A', strtotime($r['hora_salida'])); ?></div>
                            <div style="font-size:0.75rem; color:var(--gray);">Límite: <?php echo date('h:i A', strtotime($r['hora_estimada_retorno'])); ?></div>
                        </td>
                        <td>
                            <?php if($r['hora_retorno']): ?>
                                <strong style="color:#34d399;"><?php echo date('h:i A', strtotime($r['hora_retorno'])); ?></strong>
                            <?php else: ?>
                                <span style="color:var(--gray); font-style:italic;">--:--</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding-right:1.5rem;">
                            <?php 
                            if ($r['estado'] == 'Pendiente') {
                                $horaActual = date('H:i:s');
                                if ($horaActual > $r['hora_estimada_retorno']) {
                                    echo '<span class="badge badge-danger" style="animation: pulse 1.5s infinite; display:inline-flex; align-items:center; gap:5px;"><i class="fa-solid fa-triangle-exclamation"></i> SOBREPASADO</span>';
                                } else {
                                    echo '<span class="badge badge-warning" style="display:inline-flex; align-items:center; gap:5px;"><i class="fa-solid fa-hourglass-half"></i> FUERA</span>';
                                }
                            } else {
                                echo '<span class="badge badge-success" style="display:inline-flex; align-items:center; gap:5px;"><i class="fa-solid fa-check-circle"></i> DENTRO</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if(typeof filterTable === "function") {
        filterTable('permisoSearch', 'permisosTable');
    }
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
