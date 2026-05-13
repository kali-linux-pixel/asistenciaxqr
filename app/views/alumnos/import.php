<?php require APPROOT . '/views/inc/header.php'; ?>

<div style="max-width:600px; margin: 0 auto;">
    <div style="margin-bottom:1rem;">
        <a href="<?php echo URLROOT; ?>/alumnos" class="text-muted" style="font-size:0.85rem;">
            <i class="fa-solid fa-arrow-left"></i> Volver a la lista
        </a>
    </div>

    <div class="card">
        <div class="card-header" style="background:linear-gradient(135deg, #1D6F42, #27ae60); color:#fff; border-bottom:none;">
            <div class="card-title" style="color:#fff;">
                <span class="title-icon" style="background:rgba(255,255,255,0.2); color:#fff;">
                    <i class="fa-solid fa-file-excel"></i>
                </span>
                Importar desde Excel / CSV
            </div>
        </div>
        
        <div class="card-body">
            <p class="text-muted" style="font-size:0.88rem; margin-bottom:1.5rem;">
                Suba una lista de alumnos masiva ahorrando tiempo. El sistema registrará a los alumnos y les generará un código QR único automáticamente.
            </p>

            <div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:10px; padding:1rem; margin-bottom:1.5rem;">
                <h5 style="font-size:0.8rem; font-weight:800; text-transform:uppercase; color:#1e293b; margin-bottom:8px;">
                    Instrucciones de Formato
                </h5>
                <p style="font-size:0.78rem; color:#64748b; margin-bottom:10px;">
                    Su archivo CSV o Excel guardado como .csv debe contener exactamente 5 columnas en este orden:
                </p>
                <div style="overflow-x:auto;">
                    <table style="font-size:0.72rem; border-collapse:separate; border-spacing:2px; background:#fff;">
                        <tr style="background:#e2e8f0; color:#475569; font-weight:700;">
                            <td style="padding:5px 8px;">Apellidos</td>
                            <td style="padding:5px 8px;">Nombres</td>
                            <td style="padding:5px 8px;">DNI</td>
                            <td style="padding:5px 8px;">Grado</td>
                            <td style="padding:5px 8px;">Sección</td>
                        </tr>
                        <tr style="color:#64748b; font-family:monospace;">
                            <td style="padding:4px 8px; border:1px solid #e2e8f0;">Pérez Ruiz</td>
                            <td style="padding:4px 8px; border:1px solid #e2e8f0;">Juan Carlos</td>
                            <td style="padding:4px 8px; border:1px solid #e2e8f0;">76543210</td>
                            <td style="padding:4px 8px; border:1px solid #e2e8f0;">1RO</td>
                            <td style="padding:4px 8px; border:1px solid #e2e8f0;">A</td>
                        </tr>
                    </table>
                </div>
                <p style="font-size:0.7rem; color:#ef4444; margin-top:8px; font-weight:600;">
                    ⚠️ Importante: La primera fila del archivo se ignorará (asumida como títulos). El "Grado" y la "Sección" deben coincidir con los nombres ya creados en el sistema.
                </p>
            </div>

            <form action="<?php echo URLROOT; ?>/alumnos/import" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Seleccionar Archivo (.csv)</label>
                    <div style="position:relative; border:2px dashed #e2e8f0; border-radius:12px; padding:2rem; text-align:center; background:#fafafa; transition:all .2s;" id="dropzone">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:2.5rem; color:#cbd5e1; margin-bottom:10px;"></i>
                        <div style="font-size:0.85rem; color:#64748b; margin-bottom:12px;">Arrastra tu archivo aquí o haz clic para buscar</div>
                        <input type="file" name="csv_file" accept=".csv" required style="position:absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer;" onchange="document.getElementById('filename').innerText = this.files[0].name; document.getElementById('dropzone').style.borderColor='#27ae60';">
                        <div id="filename" style="font-weight:700; color:#1D6F42; font-size:0.85rem;"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg" style="width:100%; margin-top:1rem; background:linear-gradient(135deg, #1D6F42, #27ae60); border:none;">
                    <i class="fa-solid fa-upload"></i> Procesar e Importar Ahora
                </button>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
