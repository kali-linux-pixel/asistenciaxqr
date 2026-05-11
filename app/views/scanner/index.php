<?php require APPROOT . '/views/inc/header.php'; ?>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">
    <div class="glass-card">
        <h2 style="font-size: 1.2rem; margin-bottom: 1rem;">Acción a Registrar</h2>
        <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 1.5rem;">
            <label class="mode-opt" style="display:flex; align-items:center; gap:10px; padding:15px; background:var(--glass); border:1px solid var(--glass-border); border-radius:10px; cursor:pointer;">
                <input type="radio" name="mode" value="asistencia" checked> Asistencia (Entrada/Salida)
            </label>
            <label class="mode-opt" style="display:flex; align-items:center; gap:10px; padding:15px; background:var(--glass); border:1px solid var(--glass-border); border-radius:10px; cursor:pointer;">
                <input type="radio" name="mode" value="permiso_salida"> Solicitar Permiso Temporal
            </label>
            <label class="mode-opt" style="display:flex; align-items:center; gap:10px; padding:15px; background:var(--glass); border:1px solid var(--glass-border); border-radius:10px; cursor:pointer;">
                <input type="radio" name="mode" value="permiso_retorno"> Registrar Retorno
            </label>
        </div>
        
        <div id="motive-div" style="display:none; margin-bottom:1rem;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div>
                    <label>Motivo:</label>
                    <select id="permiso_motivo" class="form-control" style="background:#0f172a;">
                        <option value="Baño">Baño</option>
                        <option value="Salud">Salud</option>
                        <option value="Dirección">Dirección</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label>Tiempo Estimado:</label>
                    <select id="permiso_tiempo" class="form-control" style="background:#0f172a;">
                        <option value="5">5 Minutos</option>
                        <option value="10" selected>10 Minutos</option>
                        <option value="15">15 Minutos</option>
                        <option value="30">30 Minutos</option>
                        <option value="60">1 Hora</option>
                    </select>
                </div>
            </div>
        </div>

        <button onclick="startScanner()" class="btn btn-primary" style="width:100%; padding:15px;">Activar Cámara</button>
    </div>

    <div>
        <div class="glass-card" style="padding: 10px;">
            <div id="reader" style="width: 100%; min-height: 300px; background: #000; border-radius: 12px; overflow: hidden;"></div>
        </div>
        <div id="res-info" style="text-align: center; margin-top: 15px;"></div>
    </div>
</div>

<script>
// Generate sound natively without loading external files
const playSound = (type) => {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.connect(gain);
    gain.connect(ctx.destination);
    
    if (type === 'success') {
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, ctx.currentTime); // A5
        gain.gain.setValueAtTime(0.1, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);
        osc.start();
        osc.stop(ctx.currentTime + 0.2);
    } else {
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(150, ctx.currentTime); // Low buzz
        gain.gain.setValueAtTime(0.1, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
        osc.start();
        osc.stop(ctx.currentTime + 0.4);
    }
};

const html5QrCode = new Html5Qrcode("reader");
let isScanning = false;

function startScanner() {
    if(isScanning) return;
    html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, (token) => {
        html5QrCode.pause();
        processToken(token);
    });
    isScanning = true;
}

function processToken(token) {
    const mode = document.querySelector('input[name="mode"]:checked').value;
    const motive = document.getElementById("permiso_motivo").value;
    const duration = document.getElementById("permiso_tiempo").value;
    
    let fd = new FormData();
    fd.append('token', token);
    fd.append('mode', mode);
    fd.append('motivo', motive);
    fd.append('tiempo', duration);

    fetch('<?php echo URLROOT; ?>/scanner/process', { method:'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        const info = document.getElementById("res-info");
        if(data.status === 'success') {
            playSound('success');
            info.innerHTML = `<div class="badge badge-success" style="font-size:1rem; padding:20px; width:100%;">${data.message}<br><strong>${data.alumno}</strong></div>`;
        } else {
            playSound('error');
            info.innerHTML = `<div class="badge badge-danger" style="font-size:1rem; padding:20px; width:100%;">${data.message}</div>`;
        }
        setTimeout(() => html5QrCode.resume(), 3000);
    });
}

document.querySelectorAll('input[name="mode"]').forEach(r => {
    r.addEventListener('change', e => {
        document.getElementById("motive-div").style.display = e.target.value === 'permiso_salida' ? 'block' : 'none';
    });
});
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
