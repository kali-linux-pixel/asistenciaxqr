<?php if(isLoggedIn()): ?>
        </div><!-- /.page-body -->

        <!-- Footer institucional -->
        <div style="background:#fff; border-top:1px solid #d5dbe6; padding:0.75rem 2rem; display:flex; align-items:center; justify-content:space-between; font-size:0.72rem; color:#8a96a8;">
            <span><i class="fa-solid fa-shield-halved" style="color:#0B0B93; margin-right:4px;"></i> <?php echo SITENAME; ?> — Sistema de Control Escolar</span>
            <span><?php echo date('Y'); ?> &copy; Todos los derechos reservados</span>
        </div>

        <!-- Mobile Nav -->
        <nav class="mobile-nav">
            <?php if(isDirector()): ?>
            <a href="<?php echo URLROOT; ?>/usuarios/profesores" class="mobile-nav-item">
                <i class="fa-solid fa-chalkboard-user"></i><span>Docentes</span>
            </a>
            <a href="<?php echo URLROOT; ?>/reportes/asistencias" class="mobile-nav-item">
                <i class="fa-solid fa-clipboard-list"></i><span>Asist.</span>
            </a>
            <a href="<?php echo URLROOT; ?>/reportes/permisos" class="mobile-nav-item">
                <i class="fa-solid fa-ticket"></i><span>Permisos</span>
            </a>
            <a href="<?php echo URLROOT; ?>/alumnos" class="mobile-nav-item">
                <i class="fa-solid fa-user-graduate"></i><span>Alumnos</span>
            </a>
            <?php else: ?>
            <a href="<?php echo URLROOT; ?>/pages/index" class="mobile-nav-item">
                <i class="fa-solid fa-gauge-high"></i><span>Inicio</span>
            </a>
            <a href="<?php echo URLROOT; ?>/scanner" class="mobile-nav-item">
                <i class="fa-solid fa-qrcode"></i><span>QR</span>
            </a>
            <a href="<?php echo URLROOT; ?>/alumnos" class="mobile-nav-item">
                <i class="fa-solid fa-user-graduate"></i><span>Alumnos</span>
            </a>
            <a href="<?php echo URLROOT; ?>/reportes/asistencias" class="mobile-nav-item">
                <i class="fa-solid fa-clipboard-list"></i><span>Asist.</span>
            </a>
            <a href="<?php echo URLROOT; ?>/reportes/permisos" class="mobile-nav-item">
                <i class="fa-solid fa-ticket"></i><span>Permisos</span>
            </a>
            <?php endif; ?>
        </nav>
    </div><!-- /.main-content -->
</div><!-- /.app-layout -->
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Reloj del topbar
    const clockEl = document.getElementById('clock-time');
    if (clockEl) {
        const tick = () => {
            const n = new Date();
            clockEl.textContent = n.toLocaleTimeString('es-PE', { hour:'2-digit', minute:'2-digit', hour12: true });
        };
        tick(); setInterval(tick, 1000);
    }

    // Barra de progreso
    const bar = document.getElementById('nprogress');
    if (bar) {
        bar.style.width = '100%';
        setTimeout(() => { bar.style.opacity = '0'; setTimeout(() => bar.style.display = 'none', 300); }, 500);
        document.querySelectorAll('a[href]').forEach(link => {
            if (link.hostname === window.location.hostname && !link.href.includes('#')) {
                link.addEventListener('click', () => {
                    bar.style.display = 'block'; bar.style.opacity = '1';
                    bar.style.width = '0%';
                    setTimeout(() => bar.style.width = '65%', 10);
                });
            }
        });
    }

    // Nav activo
    const path = window.location.pathname;
    document.querySelectorAll('.nav-link, .mobile-nav-item').forEach(link => {
        const href = (link.getAttribute('href') || '').split('?')[0];
        if (href && href !== '/' && path.includes(href.split('/').pop())) {
            link.classList.add('active');
        }
    });
});

// Filtrado de tabla reutilizable
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;
    input.addEventListener('keyup', function () {
        const q = this.value.toLowerCase();
        table.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}
</script>
</body>
</html>
