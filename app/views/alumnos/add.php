<?php require APPROOT . '/views/inc/header.php'; ?>

<a href="<?php echo URLROOT; ?>/alumnos" class="btn btn-outline btn-sm" style="margin-bottom:1.25rem;">
    <i class="fa-solid fa-arrow-left"></i> Volver a Nómina
</a>

<div style="max-width:560px;">
    <div class="card">
        <div class="card-header" style="background:linear-gradient(135deg,#0B0B93,#1a3a8f);">
            <div class="card-title" style="color:#fff;">
                <span style="width:28px;height:28px;background:rgba(255,255,255,0.15);border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:0.85rem;color:#fff;">
                    <i class="fa-solid fa-user-plus"></i>
                </span>
                Ficha de Matrícula — Nuevo Alumno
            </div>
        </div>
        <div class="card-body">
            <p style="font-size:0.82rem; color:#5a6a7e; margin-bottom:1.5rem; padding:10px; background:#e8edf8; border-radius:8px;">
                <i class="fa-solid fa-circle-info" style="color:#0B0B93; margin-right:6px;"></i>
                Complete los datos del estudiante. Se generará automáticamente un código QR único para el control de asistencia.
            </p>

            <form action="<?php echo URLROOT; ?>/alumnos/add" method="POST" autocomplete="off">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label><i class="fa-solid fa-font" style="color:#0B0B93; margin-right:5px;"></i> Nombres del Alumno</label>
                        <input type="text" name="nombres" class="form-control" required placeholder="Ej. Juan Carlos">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label><i class="fa-solid fa-font" style="color:#c0392b; margin-right:5px;"></i> Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" required placeholder="Ej. García López">
                    </div>
                </div>
                <div style="height:0.9rem;"></div>

                <div class="form-group">
                    <label><i class="fa-regular fa-id-card" style="color:#f39c12; margin-right:5px;"></i> Documento de Identidad (DNI)</label>
                    <div class="input-wrap">
                        <i class="input-icon fa-solid fa-hashtag"></i>
                        <input type="text" name="dni" class="form-control" required maxlength="8" pattern="[0-9]{8}"
                               placeholder="8 dígitos" oninput="this.value=this.value.replace(/\D/g,'')">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-school" style="color:#27ae60; margin-right:5px;"></i> Aula / Grado y Sección</label>
                    <select name="grado_seccion" class="form-control" required style="cursor:pointer;">
                        <?php if(!isDirector()): ?>
                            <?php 
                                $teacherGrado = $_SESSION['user_grado_seccion_id'] ?? 0;
                                foreach($data['grades'] as $g): 
                                    if($g['id'] == $teacherGrado):
                            ?>
                                        <option value="<?php echo $g['id']; ?>" selected>
                                            <?php echo $g['grado'].' — Sección "'.$g['seccion'].'" (Tu Aula)'; ?>
                                        </option>
                            <?php 
                                    endif;
                                endforeach; 
                            ?>
                        <?php else: ?>
                            <option value="" disabled selected>Seleccione el aula...</option>
                            <?php foreach($data['grades'] as $g): ?>
                                <option value="<?php echo $g['id']; ?>">
                                    <?php echo $g['grado'].' — Sección "'.$g['seccion'].'"'; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <hr style="border:none; border-top:1px solid #e5e7eb; margin:1.25rem 0;">

                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
                    <i class="fa-solid fa-qrcode"></i> Registrar y Generar Código QR
                </button>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
