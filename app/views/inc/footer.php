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

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const progressBar = document.getElementById('top-progress-bar');
        
        // 1. Dynamic Loading Bar on Click
        document.querySelectorAll('a').forEach(link => {
            if(link.hostname === window.location.hostname && !link.getAttribute('href').includes('#')) {
                link.addEventListener('click', () => {
                    progressBar.style.width = '40%';
                    setTimeout(() => progressBar.style.width = '70%', 200);
                });
            }
        });
        
        // Finish loading animation
        progressBar.style.width = '100%';
        setTimeout(() => progressBar.style.display = 'none', 500);

        // 2. Intelligent Active Nav Highlighting
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link, .mobile-nav-item').forEach(link => {
            const href = link.getAttribute('href');
            if(href && currentPath.includes(href.replace('<?php echo URLROOT; ?>', ''))) {
                link.classList.add('active');
            }
        });
    });

    // 3. ENTERPRISE GLOBAL FILTERING FUNCTION (Reusable)
    function filterTable(inputId, tableId) {
        const input = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        if (!input || !table) return;

        input.addEventListener('keyup', function() {
            const filter = input.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) { // skip header
                let txtValue = rows[i].textContent || rows[i].innerText;
                rows[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
            }
        });
    }
    </script>
</body>
</html>
