<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="login-container">
    <div class="glass-card login-card">
        <div class="logo" style="justify-content: center; font-size: 2rem; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-qrcode"></i> QR Aula
        </div>
        <p style="text-align: center; color: var(--gray); margin-bottom: 2rem;">Inicia sesión para acceder</p>

        <?php if(!empty($data['error'])): ?>
            <div style="background: rgba(239,68,68,0.1); color: #fca5a5; padding: 12px; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; border: 1px solid rgba(239,68,68,0.3);">
                <?php echo $data['error']; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo URLROOT; ?>/usuarios/login" method="POST">
            <div class="form-group">
                <label><i class="fa-solid fa-user"></i> Usuario</label>
                <input type="text" name="usuario" class="form-control" value="<?php echo $data['usuario']; ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-lock"></i> Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; margin-top: 1rem;">
                Entrar al Sistema
            </button>
        </form>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
