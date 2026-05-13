<?php require APPROOT . '/views/inc/header.php'; ?>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<style>
:root {
    --azul: #0B0B93;
    --amarillo: #FBBF24;
    --rojo: #DC2626;
    --blanco: #FFFFFF;
}

.mode-card {
    display: flex; align-items: center; gap: 14px;
    padding: 15px 18px; border-radius: 12px;
    border: 3px solid var(--azul); cursor: pointer;
    transition: all .2s; background: var(--blanco);
    margin-bottom: 10px; box-shadow: 0 4px 0 var(--azul);
}
.mode-card:hover {
    background: var(--amarillo);
    transform: translateY(-2px);
    box-shadow: 0 6px 0 var(--azul);
}
.mode-card:has(input:checked) {
    border-color: var(--rojo);
    background: var(--blanco);
    box-shadow: 0 5px 0 var(--rojo);
}
.mode-card input { accent-color: var(--rojo); width: 18px; height: 18px; flex-shrink: 0; }
.mode-card .mode-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
    border: 2px solid var(--azul);
}
.mode-card .mode-info { flex: 1; }
.mode-card .mode-title { font-weight: 900; font-size: 0.95rem; color: var(--azul); text-transform: uppercase; }
.mode-card .mode-desc  { font-size: 0.75rem; font-weight: 600; color: var(--azul); opacity: 0.8; }

.scan-result {
    padding: 20px; border-radius: 15px; text-align: center;
    font-weight: 900; margin-top: 15px;
    animation: fadeInUp .3s ease;
    font-size: 1.1rem;
    border: 4px solid var(--azul);
}
.scan-ok  { background: var(--blanco); color: var(--azul); border-color: var(--azul); box-shadow: 0 8px 0 var(--azul); }
.scan-err { background: var(--blanco); color: var(--rojo); border-color: var(--rojo); box-shadow: 0 8px 0 var(--rojo); }

.card-header-brand {
    background: var(--azul); color: var(--blanco);
    padding: 14px 18px; border-radius: 12px 12px 0 0;
    border-bottom: 4px solid var(--amarillo);
    font-weight: 900; text-transform: uppercase;
    display: flex; align-items: center; gap: 8px;
}
.form-brand {
    border: 3px solid var(--azul) !important;
    border-radius: 10px !important;
    font-weight: 800 !important;
    color: var(--azul) !important;
    background: var(--blanco) !important;
}
.form-brand:focus {
    border-color: var(--amarillo) !important;
    outline: none;
}
</style>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start; background:var(--blanco); padding:15px; border-radius:15px;">

    <!-- Panel Control -->
    <div>
        <div class="card" style="margin-bottom:1.5rem; border: 3px solid var(--azul); box-shadow: 0 6px 0 var(--azul);">
            <div class="card-header-brand">
                <i class="fa-solid fa-sliders" style="color:var(--amarillo);"></i>
                SELECCIONAR ACCIÓN DEL SCANNER
            </div>
            <div class="card-body" style="background:var(--blanco); padding: 15px;">
                <label class="mode-card">
                    <input type="radio" name="mode" value="asistencia" checked>
                    <span class="mode-icon" style="background:var(--blanco); color:var(--azul); border-color:var(--azul);"><i class="fa-solid fa-right-to-bracket"></i></span>
                    <span class="mode-info">
                        <div class="mode-title">ASISTENCIA GENERAL</div>
                        <div class="mode-desc">Registro diario de Entradas y Salidas</div>
                    </span>
                </label>
                <label class="mode-card">
                    <input type="radio" name="mode" value="permiso_salida">
                    <span class="mode-icon" style="background:var(--blanco); color:var(--rojo); border-color:var(--rojo);"><i class="fa-solid fa-person-walking-arrow-right"></i></span>
                    <span class="mode-info">
                        <div class="mode-title">PAPELETA DE SALIDA</div>
                        <div class="mode-desc">Permiso Temporal o Permanente fuera de clase</div>
                    </span>
                </label>
                <label class="mode-card" style="margin-bottom:0;">
                    <input type="radio" name="mode" value="permiso_retorno">
                    <span class="mode-icon" style="background:var(--blanco); color:var(--azul); border-color:var(--azul);"><i class="fa-solid fa-person-walking-arrow-loop-left"></i></span>
                    <span class="mode-info">
                        <div class="mode-title">REGISTRO DE RETORNO</div>
                        <div class="mode-desc">Confirmar que el alumno volvió al salón</div>
                    </span>
                </label>
            </div>
        </div>

        <!-- Datos del Permiso -->
        <div id="motive-div" style="display:none; animation: fadeInUp 0.3s ease;">
            <div class="card" style="margin-bottom:1.5rem; border: 3px solid var(--rojo); box-shadow: 0 6px 0 var(--rojo);">
                <div class="card-header-brand" style="background:var(--rojo); border-color:var(--blanco);">
                    <i class="fa-solid fa-file-alt" style="color:var(--blanco);"></i>
                    CONFIGURAR PAPELETA DE SALIDA
                </div>
                <div class="card-body" style="background:var(--blanco); padding: 15px;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom: 12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:0.8rem; font-weight:900; color:var(--rojo); text-transform:uppercase; margin-bottom:4px; display:block;">Motivo Principal</label>
                            <select id="permiso_motivo" class="form-control form-brand" style="cursor:pointer;">
                                <option value="SS.HH. (Baño)">🚾 Baño / SS.HH.</option>
                                <option value="Enfermería / Tópico">🏥 Enfermería / Tópico</option>
                                <option value="Llamado Dirección">👔 Dirección</option>
                                <option value="Coordinación Académica">📚 Coordinación</option>
                                <option value="Indisciplina / Retiro">⚠️ Retiro de Aula</option>
                                <option value="Otros">🔰 Otro Motivo</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:0.8rem; font-weight:900; color:var(--rojo); text-transform:uppercase; margin-bottom:4px; display:block;">Duración</label>
                            <select id="permiso_tiempo" class="form-control form-brand" style="cursor:pointer;">
                                <option value="5">5 minutos</option>
                                <option value="10" selected>10 minutos</option>
                                <option value="15">15 minutos</option>
                                <option value="30">30 minutos</option>
                                <option value="60">1 hora</option>
                                <option value="999">PERMANENTE (Retiro/Salud)</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Motivo Personalizado Escrito -->
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-size:0.8rem; font-weight:900; color:var(--azul); text-transform:uppercase; margin-bottom:4px; display:block;">Especificar Detalle (Opcional)</label>
                        <input type="text" id="permiso_motivo_custom" class="form-control form-brand" placeholder="Escriba detalle o descripción adicional aquí...">
                    </div>
                </div>
            </div>
        </div>

        <button onclick="startScanner()" id="scanBtn" class="btn btn-primary btn-lg" style="width:100%;">
            <i class="fa-solid fa-camera"></i> Activar Cámara
        </button>

        <!-- Ingreso por DNI Manual -->
        <div class="card" style="margin-top:1rem; animation: fadeInUp 0.4s ease; border-top: 4px solid var(--amarillo);">
            <div class="card-header" style="background:#fff; padding: 12px 16px;">
                <div style="font-weight:800; font-size:0.85rem; color: var(--azul);">
                    <i class="fa-solid fa-id-card" style="color:var(--amarillo);"></i> Registro Manual (Sin Carnet)
                </div>
            </div>
            <div class="card-body" style="padding: 14px 16px; background:#fafbfd;">
                <div style="display:flex; gap:8px;">
                    <input type="text" id="manual_dni" class="form-control" 
                           placeholder="Ingresar DNI del Alumno..." 
                           maxlength="15" 
                           onkeypress="if(event.key === 'Enter') processManualDni()"
                           style="text-align:center; font-weight:800; font-size:1rem; border:2px solid #cbd5e1; flex:1;">
                    <button onclick="processManualDni()" class="btn btn-success" style="padding: 0 16px; border-radius:10px; flex-shrink:0;">
                        <i class="fa-solid fa-share"></i> Registrar
                    </button>
                </div>
                <div style="font-size:0.7rem; color:#64748b; margin-top:6px; font-style:italic;">
                    * Ingrese el DNI si el alumno no cuenta con su código QR hoy.
                </div>
            </div>
        </div>

        <div id="res-info"></div>
    </div>

    <!-- Visor QR -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <span class="title-icon" style="background:#0B0B93;"><i class="fa-solid fa-camera"></i></span>
                    Lector de Código QR
                </div>
            </div>
            <div style="padding:8px;">
                <div id="reader" style="width:100%; min-height:300px; background:#1a2332; border-radius:10px; overflow:hidden;"></div>
            </div>
        </div>
        <div style="text-align:center; padding:10px; font-size:0.75rem; color:#9ca3af;">
            <i class="fa-solid fa-lightbulb" style="color:#f39c12; margin-right:4px;"></i>
            Acerque el carnet del alumno a la cámara para escanear su QR
        </div>
    </div>
</div>

<!-- Estilo para el flash visual de éxito/error -->
<div id="scan-flash" style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:99999; pointer-events:none; opacity:0; transition:opacity 0.1s;"></div>

<script>
// --- Generador de Sonido Profesional ---
const playSound = (type) => {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const masterGain = audioCtx.createGain();
        masterGain.connect(audioCtx.destination);

        const playTone = (freq, typeTone, duration, vol, startTime = 0) => {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = typeTone;
            osc.frequency.setValueAtTime(freq, audioCtx.currentTime + startTime);
            gain.gain.setValueAtTime(vol, audioCtx.currentTime + startTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + startTime + duration);
            osc.connect(gain);
            gain.connect(masterGain);
            osc.start(audioCtx.currentTime + startTime);
            osc.stop(audioCtx.currentTime + startTime + duration);
        };

        if (type === 'success') {
            // Tono doble agudo optimista "Bip-Bip!"
            playTone(950, 'sine', 0.1, 0.15, 0);
            playTone(1200, 'sine', 0.2, 0.15, 0.12);
        } else {
            // Tono grave de alerta "Bwooooom"
            playTone(250, 'sawtooth', 0.5, 0.2, 0);
            playTone(180, 'sawtooth', 0.5, 0.1, 0.1);
        }
    } catch (e) { console.warn("Audio contextual error", e); }
};

// --- Función de Flash Visual de Pantalla ---
function triggerFlash(isSuccess) {
    const flash = document.getElementById('scan-flash');
    flash.style.background = isSuccess ? 'rgba(39, 174, 96, 0.3)' : 'rgba(192, 57, 43, 0.4)';
    flash.style.opacity = '1';
    setTimeout(() => {
        flash.style.transition = 'opacity 0.4s ease-out';
        flash.style.opacity = '0';
        setTimeout(() => { flash.style.transition = 'opacity 0.1s'; }, 400);
    }, 150);
}

const qr = new Html5Qrcode("reader");
let scannerOn = false;

function startScanner() {
    if (scannerOn) return;
    const btn = document.getElementById('scanBtn');
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Buscando Código QR...';
    btn.style.background = '#27ae60';
    btn.style.borderColor = '#27ae60';
    
    // CONFIGURACIÓN ULTRA-RÁPIDA: Aumentamos FPS a 24 para escaneo instantáneo
    qr.start(
        { facingMode: "environment" }, 
        { 
            fps: 24,       // Más frames por segundo = detección instantánea
            qrbox: 250, 
            aspectRatio: 1.0 
        }, 
        decodedText => {
            qr.pause(); // Congela para procesar
            processScan(decodedText);
        }
    ).then(() => {
        scannerOn = true;
    }).catch(err => {
        alert("No se pudo acceder a la cámara. Revisa los permisos del navegador.");
        btn.innerHTML = '<i class="fa-solid fa-camera"></i> Intentar de Nuevo';
    });
}

function processManualDni() {
    const dniInput = document.getElementById('manual_dni');
    const dniVal = dniInput.value.trim();
    if (dniVal === '') {
        alert("Por favor, escriba el DNI del alumno.");
        return;
    }
    // Pausar escáner si estuviese corriendo
    if (scannerOn) {
        try { qr.pause(); } catch(e){}
    }
    processScan('', dniVal);
    dniInput.value = ''; // Limpiar campo tras procesar
}

let lastScannedToken = '';
let lastScanTime = 0;

function processScan(token, dni = '') {
    const identifier = token || dni;
    const now = Date.now();

    // ==========================================
    // PROTECCIÓN ANTI-DUPLICADOS: Bloquear re-escaneos en < 8 segundos
    // ==========================================
    if (identifier === lastScannedToken && (now - lastScanTime) < 8000) {
        playSound('error');
        const resDiv = document.getElementById('res-info');
        resDiv.innerHTML = `<div class="scan-result scan-err" style="font-size:0.9rem;">
            ⚠️ OPERACIÓN BLOQUEADA POR SEGURIDAD:<br>El alumno fue escaneado hace un instante.<br>Espere 8 segundos antes de repetir.
        </div>`;
        setTimeout(() => {
            resDiv.innerHTML = '';
            if (scannerOn) { try { qr.resume(); } catch(e){} }
        }, 4000);
        return;
    }

    lastScannedToken = identifier;
    lastScanTime = now;

    const resDiv = document.getElementById('res-info');
    resDiv.innerHTML = '<div style="text-align:center; padding:1rem; color:var(--azul); font-weight:bold;"><i class="fa-solid fa-circle-notch fa-spin"></i> PROCESANDO TRANSACCIÓN...</div>';

    // Construir Motivo Final (Dropdown + Escrito)
    let finalMotivo = document.getElementById('permiso_motivo').value;
    const escritoVal = document.getElementById('permiso_motivo_custom').value.trim();
    if (escritoVal !== '') {
        finalMotivo += ` (${escritoVal})`; // Agregar aclaración escrita
    }

    const formData = new FormData();
    formData.append('token', token);
    formData.append('dni', dni);
    formData.append('mode', document.querySelector('input[name="mode"]:checked').value);
    formData.append('motivo', finalMotivo);
    formData.append('tiempo', document.getElementById('permiso_tiempo').value);

    fetch('<?php echo URLROOT; ?>/scanner/process', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const isOk = (data.status === 'success');
        
        // 1. Reproducir Sonido
        playSound(isOk ? 'success' : 'error');
        
        // 2. Flash Visual
        triggerFlash(isOk);
        
        // 3. Actualizar interfaz con barra de cooldown
        const cooldownTime = isOk ? 3000 : 4500; // Milisegundos de pausa

        resDiv.innerHTML = `
            <div class="scan-result ${isOk ? 'scan-ok' : 'scan-err'}">
                <i class="fa-solid ${isOk ? 'fa-circle-check' : 'fa-circle-xmark'}" style="font-size:2.5rem;display:block;margin-bottom:10px;"></i>
                <div style="font-size:1.1rem; line-height:1.4;">${data.message}</div>
                ${isOk ? '<div style="font-size:1.4rem; font-weight:800; color:#0B0B93; margin-top:5px;">'+data.alumno+'</div>' : ''}
                <div style="margin-top:15px; height:4px; background:rgba(0,0,0,0.1); border-radius:2px; overflow:hidden;">
                    <div id="cooldown-bar" style="height:100%; background:${isOk ? '#1a7a48':'#c0392b'}; width:100%; transition: width ${cooldownTime}ms linear;"></div>
                </div>
                <div style="font-size:0.7rem; margin-top:5px; opacity:0.6;">El escáner se reanudará automáticamente</div>
            </div>
        `;
        
        // Iniciar animación de barra
        requestAnimationFrame(() => {
            const bar = document.getElementById('cooldown-bar');
            if(bar) bar.style.width = '0%';
        });

        // 4. Resumir cámara automáticamente tras cooldown
        setTimeout(() => {
            resDiv.innerHTML = '';
            if (scannerOn) {
                try { qr.resume(); } catch(e){}
            }
        }, cooldownTime);
    })
    .catch(err => {
        playSound('error');
        triggerFlash(false);
        resDiv.innerHTML = `<div class="scan-result scan-err"><i class="fa-solid fa-triangle-exclamation"></i> Error de Conexión</div>`;
        setTimeout(() => { 
            resDiv.innerHTML = ''; 
            if (scannerOn) {
                try { qr.resume(); } catch(e){}
            }
        }, 3000);
    });
}

document.querySelectorAll('input[name="mode"]').forEach(r => {
    r.addEventListener('change', e => {
        document.getElementById('motive-div').style.display =
            e.target.value === 'permiso_salida' ? 'block' : 'none';
    });
});

// AUTOMATIZACIÓN INTELIGENTE ENFERMERÍA / RETIRO PERMANENTE
document.getElementById('permiso_motivo').addEventListener('change', function() {
    const timeSelect = document.getElementById('permiso_tiempo');
    const val = this.value;
    
    if (val.includes('Enfermería') || val.includes('Retiro')) {
        timeSelect.value = '999'; // Forzar valor Permanente
        timeSelect.style.background = 'var(--rojo)';
        timeSelect.style.color = 'var(--blanco)';
    } else {
        if(timeSelect.value === '999') timeSelect.value = '10'; // Restaurar a default
        timeSelect.style.background = 'var(--blanco)';
        timeSelect.style.color = 'var(--azul)';
    }
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
