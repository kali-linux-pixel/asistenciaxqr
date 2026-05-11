<?php require APPROOT . '/views/inc/header.php'; ?>

<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <!-- Search Box Engine -->
    <div style="position: relative; flex: 1; max-width: 400px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--gray);"></i>
        <input type="text" id="searchInput" placeholder="Buscar por nombre, DNI o grado..." style="width: 100%; padding: 12px 16px 12px 40px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 50px; color: #fff; outline: none; transition: border 0.3s;" onfocus="this.style.borderColor='#6366f1'; this.style.background='rgba(255,255,255,0.05)'" onblur="this.style.borderColor='var(--glass-border)'; this.style.background='rgba(255,255,255,0.03)'">
    </div>
    
    <a href="<?php echo URLROOT; ?>/alumnos/add" class="btn btn-primary" style="border-radius: 50px; box-shadow: 0 4px 15px rgba(99,102,241,0.3);">
        <i class="fa-solid fa-user-plus"></i> Registrar Nuevo
    </a>
</div>

<?php flash('alumno_message'); ?>

<div class="glass-card" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table id="alumnosTable" style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="padding-left: 2rem;">Matrícula</th>
                    <th>Alumno / Estudiante</th>
                    <th>Identificación</th>
                    <th>Nivel / Grado</th>
                    <th>Condición</th>
                    <th style="text-align: right; padding-right: 2rem;">Acciones VIP</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['alumnos'] as $s): ?>
                    <tr style="transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding-left: 2rem; color: var(--primary); font-weight: 700;">
                            #ALM-<?php echo str_pad($s['id'], 4, "0", STR_PAD_LEFT); ?>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap: 12px;">
                                <div style="width:35px; height:35px; border-radius:50%; background:linear-gradient(135deg, #6366f1, #a855f7); display:flex; align-items:center; justify-content:center; font-weight:bold; color:white; font-size:0.8rem;">
                                    <?php echo substr($s['apellidos'], 0, 1) . substr($s['nombres'], 0, 1); ?>
                                </div>
                                <span style="font-weight: 600;"><?php echo htmlspecialchars($s['apellidos'] . ', ' . $s['nombres']); ?></span>
                            </div>
                        </td>
                        <td><i class="fa-solid fa-address-card" style="color: var(--gray); margin-right: 6px; font-size: 0.85rem;"></i> <?php echo $s['dni']; ?></td>
                        <td><span style="background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 6px; font-size: 0.85rem;"><?php echo $s['grado'] . ' - "' . $s['seccion'] . '"'; ?></span></td>
                        <td>
                            <?php echo ($s['estado'] == 1) ? '<span class="badge badge-success" style="padding: 5px 12px;"><i class="fa-solid fa-circle" style="font-size:0.5rem; margin-right:5px;"></i> Activo</span>' : '<span class="badge badge-danger">Baja</span>'; ?>
                        </td>
                        <td style="text-align: right; padding-right: 2rem;">
                            <a href="<?php echo URLROOT; ?>/alumnos/imprimir/<?php echo $s['id']; ?>" target="_blank" class="btn btn-outline" style="padding: 8px 15px; border-radius: 8px; background: rgba(99,102,241,0.1); border-color: rgba(99,102,241,0.2); color: #a5b4fc;" title="Imprimir Carnet QR">
                                <i class="fa-solid fa-qrcode" style="margin-right: 5px;"></i> Carnet
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Auto-Search Logic Injection -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    if(typeof filterTable === "function"){
        filterTable('searchInput', 'alumnosTable');
    }
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
