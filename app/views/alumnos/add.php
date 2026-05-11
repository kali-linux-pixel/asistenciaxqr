<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="glass-card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="margin-bottom: 2rem; text-align: center;">Registrar Nuevo Alumno</h2>
    <form action="<?php echo URLROOT; ?>/alumnos/add" method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label>Nombres</label>
                <input type="text" name="nombres" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Apellidos</label>
                <input type="text" name="apellidos" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label>DNI</label>
            <input type="text" name="dni" class="form-control">
        </div>
        <div class="form-group">
            <label>Grado y Sección</label>
            <select name="grado_seccion" class="form-control" required style="background:#0f172a;">
                <?php foreach($data['grades'] as $g): ?>
                    <option value="<?php echo $g['id']; ?>"><?php echo $g['grado'] . ' ' . $g['seccion']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 1rem;">
            Guardar Alumno
        </button>
    </form>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
