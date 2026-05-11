<?php require APPROOT . '/views/inc/header.php'; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Filter Control Box -->
    <div class="glass-card" style="padding: 1.5rem; display:flex; align-items:center;">
        <form method="GET" action="<?php echo URLROOT; ?>/reportes/asistencias" style="width:100%; display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
            <div style="flex:1; min-width:140px;">
                <label style="margin-bottom: 3px; font-size:0.8rem;">Fecha:</label>
                <input type="date" name="fecha" class="form-control" value="<?php echo $data['fecha']; ?>" style="background:rgba(0,0,0,0.2); border-radius:8px; padding:10px;">
            </div>
            <div style="flex:1; min-width:180px;">
                <label style="margin-bottom: 3px; font-size:0.8rem;">Filtrar por Grado/Sección:</label>
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
            <input type="text" id="reportSearch" placeholder="Búsqueda rápida..." style="width:100%; padding:12px 12px 12px 35px; border-radius:8px; background:rgba(255,255,255,0.02); border:1px solid var(--glass-border); color:#fff; outline:none;">
        </div>
        <a href="<?php echo URLROOT; ?>/reportes/exportar?tipo=asistencia&fecha=<?php echo $data['fecha']; ?>&grado_seccion=<?php echo $data['grado_seccion']; ?>" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); height:45px; border-radius:8px; font-weight:700;">
            <i class="fa-solid fa-file-excel"></i> Exportar
        </a>
    </div>
</div>

<!-- Data Table Container -->
<div class="glass-card" style="padding:0; overflow:hidden; border-radius:15px;">
    <div style="padding:1.5rem; border-bottom:1px solid var(--glass-border); background:rgba(255,255,255,0.01);">
        <h3 style="margin:0; font-size:1rem;"><i class="fa-solid fa-table-list" style="color:var(--primary); margin-right:8px;"></i> Registros del Día (<?php echo date('d/m/Y', strtotime($data['fecha'])); ?>)</h3>
    </div>
    <div style="overflow-x: auto;">
        <table id="recordsTable" style="margin:0;">
            <thead>
                <tr style="background: rgba(0,0,0,0.1);">
                    <th style="padding-left:1.5rem;">Alumno Matriculado</th>
                    <th>Sección / Aula</th>
                    <th><i class="fa-solid fa-sign-in-alt" style="color:#10b981; margin-right:5px;"></i> Hora Entrada</th>
                    <th style="padding-right:1.5rem;"><i class="fa-solid fa-sign-out-alt" style="color:#f43f5e; margin-right:5px;"></i> Hora Salida</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($data['registros'])): ?>
                    <tr><td colspan="4" style="text-align:center; color:var(--gray); padding:40px;">No se encontraron registros para esta fecha.</td></tr>
                <?php else: ?>
                    <?php foreach($data['registros'] as $r): ?>
                    <tr style="transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                        <td style="padding-left:1.5rem;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:30px; height:30px; background:rgba(99,102,241,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--primary); font-weight:bold; font-size:0.75rem;">
                                    <?php echo substr($r['apellidos'],0,1); ?>
                                </div>
                                <span style="font-weight:600;"><?php echo htmlspecialchars($r['apellidos'] . ', ' . $r['nombres']); ?></span>
                            </div>
                        </td>
                        <td><span style="background:rgba(255,255,255,0.05); padding:3px 8px; border-radius:5px; font-size:0.8rem;"><?php echo $r['grado'] . ' - ' . $r['seccion']; ?></span></td>
                        <td style="color:#34d399; font-weight:700;"><?php echo date('h:i A', strtotime($r['hora_entrada'])); ?></td>
                        <td style="padding-right:1.5rem;">
                            <?php if($r['hora_salida']): ?>
                                <span style="color:#fca5a5; font-weight:700;"><?php echo date('h:i A', strtotime($r['hora_salida'])); ?></span>
                            <?php else: ?>
                                <span style="color:var(--gray); font-style:italic; font-size:0.85rem;">Pendiente</span>
                            <?php endif; ?>
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
        filterTable('reportSearch', 'recordsTable');
    }
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
