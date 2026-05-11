<?php
$alumno = $data['alumno'];
$qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($alumno['qr_token']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Imprimir - <?php echo htmlspecialchars($alumno['nombres']); ?></title>
    <style>
        body { font-family: system-ui; display: flex; justify-content: center; align-items: center; height: 100vh; background:#eee;}
        .card { background:#fff; border:2px solid #000; padding:20px; border-radius:10px; text-align:center; width:300px;}
        .name { font-weight:bold; text-transform:uppercase; font-size:1.2rem; margin:10px 0;}
        @media print { .no-p {display:none;} body{background:#fff;} }
    </style>
</head>
<body>
    <button class="no-p" onclick="window.print()" style="position:fixed; top:20px; right:20px; padding:10px 20px; background:#6366f1; color:#fff; border:none; cursor:pointer; border-radius:5px;">Imprimir</button>
    <div class="card">
        <h2 style="margin:0 0 15px; font-size:1rem;">CARNÉ ESTUDIANTIL</h2>
        <img src="<?php echo $qrImageUrl; ?>" width="200">
        <div class="name"><?php echo htmlspecialchars($alumno['apellidos'] . ' ' . $alumno['nombres']); ?></div>
        <div style="background:#f3f4f6; display:inline-block; padding:5px 10px; border-radius:5px;"><?php echo $alumno['grado'] . ' ' . $alumno['seccion']; ?></div>
    </div>
</body>
</html>
