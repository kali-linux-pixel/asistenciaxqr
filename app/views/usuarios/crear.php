<?php require APPROOT . '/views/inc/header.php'; ?>

<a href="<?php echo URLROOT; ?>/usuarios/profesores" class="btn btn-outline btn-sm" style="margin-bottom:1.25rem;">
    <i class="fa-solid fa-arrow-left"></i> Volver a Docentes
</a>

<div style="max-width:580px;">
    <div class="card">
        <div class="card-header" style="background:linear-gradient(135deg,#0B0B93,#1a3a8f);">
            <div class="card-title" style="color:#fff;">
                <span style="width:28px;height:28px;background:rgba(255,255,255,0.15);border-radius:7px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-user-plus"></i>
                </span>
                Registrar Nuevo Docente
            </div>
        </div>
        <div class="card-body">
            <div style="background:#e8edf8;border-radius:10px;padding:12px 14px;margin-bottom:1.5rem;font-size:0.82rem;color:#1a3a8f;display:flex;gap:10px;align-items:flex-start;">
                <i class="fa-solid fa-circle-info" style="margin-top:2px;flex-shrink:0;"></i>
                <span>El docente podrá iniciar sesión usando su <strong>correo institucional</strong> y la contraseña que usted asigne. 
                El sistema generará automáticamente el nombre de usuario a partir del correo.</span>
            </div>

            <form action="<?php echo URLROOT; ?>/usuarios/crear" method="POST" autocomplete="off">
                <!-- Nombre -->
                <div class="form-group">
                    <label><i class="fa-solid fa-user" style="color:#0B0B93;margin-right:5px;"></i> Nombre completo del Docente</label>
                    <input type="text" name="nombre" class="form-control <?php echo $data['error_nombre']?'border-danger':''; ?>"
                           value="<?php echo htmlspecialchars($data['nombre']); ?>"
                           required placeholder="Ej. María Torres Salinas">
                    <?php if($data['error_nombre']): ?>
                        <div style="color:#c0392b;font-size:0.78rem;margin-top:4px;"><i class="fa-solid fa-exclamation-circle"></i> <?php echo $data['error_nombre']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Email institucional -->
                <div class="form-group">
                    <label><i class="fa-solid fa-envelope" style="color:#c0392b;margin-right:5px;"></i> Correo Institucional (único)</label>
                    <div class="input-wrap">
                        <i class="input-icon fa-solid fa-at"></i>
                        <input type="email" name="email" class="form-control <?php echo $data['error_email']?'border-danger':''; ?>"
                               value="<?php echo htmlspecialchars($data['email']); ?>"
                               required placeholder="docente@colegio.edu.pe">
                    </div>
                    <?php if($data['error_email']): ?>
                        <div style="color:#c0392b;font-size:0.78rem;margin-top:4px;"><i class="fa-solid fa-exclamation-circle"></i> <?php echo $data['error_email']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Aula asignada -->
                <div class="form-group">
                    <label><i class="fa-solid fa-school" style="color:#f39c12;margin-right:5px;"></i> Aula / Sección Asignada</label>
                    <select name="grado_seccion" class="form-control <?php echo $data['error_grado']?'border-danger':''; ?>" required style="cursor:pointer;">
                        <option value="" disabled <?php echo empty($data['grado_seccion_id'])?'selected':''; ?>>Seleccione el aula...</option>
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

                <!-- Contraseña -->
                <div class="form-group">
                    <label><i class="fa-solid fa-key" style="color:#27ae60;margin-right:5px;"></i> Contraseña de Acceso</label>
                    <div class="input-wrap">
                        <i class="input-icon fa-solid fa-lock"></i>
                        <input type="password" name="password" id="passInput"
                               class="form-control <?php echo $data['error_pass']?'border-danger':''; ?>"
                               required placeholder="Mínimo 6 caracteres" minlength="6">
                    </div>
                    <?php if($data['error_pass']): ?>
                        <div style="color:#c0392b;font-size:0.78rem;margin-top:4px;"><i class="fa-solid fa-exclamation-circle"></i> <?php echo $data['error_pass']; ?></div>
                    <?php endif; ?>
                    <div style="margin-top:5px;display:flex;align-items:center;gap:6px;">
                        <input type="checkbox" id="showPass" onchange="document.getElementById('passInput').type=this.checked?'text':'password'">
                        <label for="showPass" style="margin:0;font-size:0.78rem;font-weight:400;color:#5a6a7e;cursor:pointer;">Mostrar contraseña</label>
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid #e5e7eb;margin:1.25rem 0;">
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
                    <i class="fa-solid fa-user-check"></i> Crear Cuenta de Docente
                </button>
            </form>
        </div>
    </div>
</div>

<style>.border-danger { border-color: #c0392b !important; }</style>
<?php require APPROOT . '/views/inc/footer.php'; ?>
