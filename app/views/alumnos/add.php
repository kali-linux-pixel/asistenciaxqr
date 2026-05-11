<?php require APPROOT . '/views/inc/header.php'; ?>

<div style="max-width: 600px; margin: 0 auto;">
    <!-- Back Button Banner -->
    <a href="<?php echo URLROOT; ?>/alumnos" style="display:inline-flex; align-items:center; gap:8px; color:var(--gray); margin-bottom: 1.5rem; text-decoration:none; font-weight:600; font-size:0.9rem;">
        <i class="fa-solid fa-arrow-left"></i> Volver al listado
    </a>

    <div class="glass-card" style="border-top: 4px solid var(--primary); box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
        <div style="text-align:center; margin-bottom: 2rem;">
            <div style="width:60px; height:60px; background:rgba(99,102,241,0.2); color:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.8rem; margin:0 auto 15px;">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <h2 style="font-weight: 700; color:#fff;">Ficha de Inscripción</h2>
            <p style="color:var(--gray); font-size:0.9rem;">Complete los datos oficiales del estudiante.</p>
        </div>

        <form action="<?php echo URLROOT; ?>/alumnos/add" method="POST" autocomplete="off">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group" style="margin:0;">
                    <label><i class="fa-solid fa-signature" style="margin-right:5px; color:var(--primary);"></i> Nombres</label>
                    <input type="text" name="nombres" class="form-control" required placeholder="Ej. Roberto" style="transition: all 0.3s;" onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 10px rgba(99,102,241,0.2)'" onblur="this.style.borderColor='var(--glass-border)'; this.style.boxShadow='none'">
                </div>
                <div class="form-group" style="margin:0;">
                    <label><i class="fa-solid fa-signature" style="margin-right:5px; color:var(--primary);"></i> Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" required placeholder="Ej. García Pérez" style="transition: all 0.3s;" onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 10px rgba(99,102,241,0.2)'" onblur="this.style.borderColor='var(--glass-border)'; this.style.boxShadow='none'">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-address-card" style="margin-right:5px; color:var(--primary);"></i> Documento DNI</label>
                <input type="text" name="dni" class="form-control" required maxlength="8" pattern="[0-9]{8}" placeholder="Ingrese 8 dígitos" style="transition: all 0.3s;" onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 10px rgba(99,102,241,0.2)'" onblur="this.style.borderColor='var(--glass-border)'; this.style.boxShadow='none'">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-school" style="margin-right:5px; color:var(--primary);"></i> Asignación Académica</label>
                <div style="position:relative;">
                    <select name="grado_seccion" class="form-control" required style="appearance: none; background: #1e293b; border: 1px solid var(--glass-border); padding-right: 40px; cursor:pointer;">
                        <option value="" disabled selected>Seleccione Grado y Sección...</option>
                        <?php foreach($data['grades'] as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo $g['grado'] . ' - Sección "' . $g['seccion'] . '"'; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fa-solid fa-chevron-down" style="position:absolute; right: 15px; top:50%; transform:translateY(-50%); color:var(--gray); pointer-events:none;"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; border-radius: 12px; font-size: 1rem; margin-top: 1.5rem; box-shadow: 0 8px 25px rgba(99,102,241,0.3);">
                <i class="fa-solid fa-cloud-arrow-up" style="margin-right:8px;"></i> Confirmar y Generar QR
            </button>
        </form>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
