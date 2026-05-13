<?php require APPROOT . '/views/inc/header.php'; ?>

<a href="<?php echo URLROOT; ?>/usuarios/profesores" class="btn btn-outline btn-sm" style="margin-bottom:1.25rem;">
    <i class="fa-solid fa-arrow-left"></i> Volver a Docentes
</a>

<div style="max-width:580px;">
    <div class="card">
        <div class="card-header" style="background:linear-gradient(135deg,#27ae60,#1e8449);">
            <div class="card-title" style="color:#fff;">
                <span style="width:28px;height:28px;background:rgba(255,255,255,0.15);border-radius:7px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-pen"></i>
                </span>
                Editar Datos del Docente
            </div>
        </div>
        <div class="card-body">
            <form action="<?php echo URLROOT; ?>/usuarios/editar/<?php echo $data['id']; ?>" method="POST" autocomplete="off">
                <!-- Nombre -->
                <div class="form-group">
                    <label><i class="fa-solid fa-user" style="color:#0B0B93;margin-right:5px;"></i> Nombre completo</label>
                    <input type="text" name="nombre" class="form-control <?php echo $data['error_nombre']?'border-danger':''; ?>"
                           value="<?php echo htmlspecialchars($data['nombre']); ?>" required>
                    <?php if($data['error_nombre']): ?>
                        <div style="color:#c0392b;font-size:0.78rem;margin-top:4px;"><i class="fa-solid fa-exclamation-circle"></i> <?php echo $data['error_nombre']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label><i class="fa-solid fa-envelope" style="color:#c0392b;margin-right:5px;"></i> Correo Institucional</label>
                    <div class="input-wrap">
                        <i class="input-icon fa-solid fa-at"></i>
                        <input type="email" name="email" class="form-control <?php echo $data['error_email']?'border-danger':''; ?>"
                               value="<?php echo htmlspecialchars($data['email']); ?>" required>
                    </div>
                    <?php if($data['error_email']): ?>
                        <div style="color:#c0392b;font-size:0.78rem;margin-top:4px;"><i class="fa-solid fa-exclamation-circle"></i> <?php echo $data['error_email']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Aula -->
                <div class="form-group">
                    <label><i class="fa-solid fa-school" style="color:#f39c12;margin-right:5px;"></i> Aula / Sección Asignada</label>
                    <select name="grado_seccion" class="form-control <?php echo $data['error_grado']?'border-danger':''; ?>" required style="cursor:pointer;">
                        <option value="" disabled>Seleccione...</option>
                        <?php foreach($data['grados'] as $g): ?>
                            <option value="<?php echo $g['id']; ?>" <?php echo ($data['grado_seccion_id']==$g['id'])?'selected':''; ?>>
                                <?php echo $g['grado'].' — Sección &ldquo;'.$g['seccion'].'&rdquo;'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if($data['error_grado']): ?>
                        <div style="color:#c0392b;font-size:0.78rem;margin-top:4px;"><i class="fa-solid fa-exclamation-circle"></i> <?php echo $data['error_grado']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Nueva contraseña (opcional) -->
                <div class="form-group">
                    <label><i class="fa-solid fa-key" style="color:#27ae60;margin-right:5px;"></i> Nueva Contraseña <small style="font-weight:400;color:#9ca3af;">(dejar vacío para no cambiar)</small></label>
                    <div class="input-wrap">
                        <i class="input-icon fa-solid fa-lock"></i>
                        <input type="password" name="password" id="passEdit"
                               class="form-control <?php echo $data['error_pass']?'border-danger':''; ?>"
                               placeholder="Nueva contraseña (opcional)">
                    </div>
                    <?php if($data['error_pass']): ?>
                        <div style="color:#c0392b;font-size:0.78rem;margin-top:4px;"><i class="fa-solid fa-exclamation-circle"></i> <?php echo $data['error_pass']; ?></div>
                    <?php endif; ?>
                    <div style="margin-top:5px;display:flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="showPassEdit" onchange="document.getElementById('passEdit').type=this.checked?'text':'password'">
                        <label for="showPassEdit" style="margin:0;font-size:0.78rem;font-weight:400;color:#5a6a7e;cursor:pointer;">Mostrar contraseña</label>
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid #e5e7eb;margin:1.25rem 0;">
                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary" style="flex:1;">
                        <i class="fa-solid fa-save"></i> Guardar Cambios
                    </button>
                    <a href="<?php echo URLROOT; ?>/usuarios/profesores" class="btn btn-outline">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.border-danger { border-color: #c0392b !important; }</style>
<?php require APPROOT . '/views/inc/footer.php'; ?>
