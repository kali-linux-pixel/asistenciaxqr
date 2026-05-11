    <?php if(isLoggedIn()) : ?>
        </main>
        
        <!-- Mobile Navigation Footer -->
        <nav class="mobile-nav">
            <a href="<?php echo URLROOT; ?>/pages/index" class="mobile-nav-item">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Inicio</span>
            </a>
            <a href="<?php echo URLROOT; ?>/scanner" class="mobile-nav-item">
                <i class="fa-solid fa-qrcode"></i>
                <span>Escanear</span>
            </a>
            <a href="<?php echo URLROOT; ?>/alumnos" class="mobile-nav-item">
                <i class="fa-solid fa-user-graduate"></i>
                <span>Alumnos</span>
            </a>
            <a href="<?php echo URLROOT; ?>/reportes/asistencias" class="mobile-nav-item">
                <i class="fa-solid fa-list-check"></i>
                <span>Reporte</span>
            </a>
        </nav>
    </div>
    <?php endif; ?>

</body>
</html>
