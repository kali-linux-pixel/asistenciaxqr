<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITENAME; ?> - <?php echo $data['title'] ?? ''; ?></title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php if(isLoggedIn()) : ?>
    <div class="app-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <i class="fa-solid fa-qrcode"></i> QR Aula
            </div>
            
            <ul class="nav-menu">
                <li>
                    <a href="<?php echo URLROOT; ?>/pages/index" class="nav-link">
                        <i class="fa-solid fa-chart-pie"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?php echo URLROOT; ?>/scanner" class="nav-link">
                        <i class="fa-solid fa-camera"></i> Escanear QR
                    </a>
                </li>
                <li>
                    <a href="<?php echo URLROOT; ?>/alumnos" class="nav-link">
                        <i class="fa-solid fa-user-graduate"></i> Alumnos
                    </a>
                </li>
                <li>
                    <a href="<?php echo URLROOT; ?>/reportes/asistencias" class="nav-link">
                        <i class="fa-solid fa-clipboard-check"></i> Asistencias
                    </a>
                </li>
                <li>
                    <a href="<?php echo URLROOT; ?>/reportes/permisos" class="nav-link">
                        <i class="fa-solid fa-person-walking-arrow-right"></i> Permisos
                    </a>
                </li>
            </ul>

            <div class="user-info">
                <div style="font-weight: 600; color: #fff; margin-bottom: 4px;">
                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </div>
                <a href="<?php echo URLROOT; ?>/usuarios/logout" class="btn btn-outline" style="width: 100%; padding: 8px; font-size: 0.8rem; margin-top: 10px;">
                    <i class="fa-solid fa-sign-out-alt"></i> Salir
                </a>
            </div>
        </aside>
        
        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h1><?php echo $data['title'] ?? 'Panel Principal'; ?></h1>
                    <p><?php echo date('d \d\e F, Y'); ?></p>
                </div>
            </div>
    <?php endif; ?>
