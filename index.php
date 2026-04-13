<?php

    require_once'public/header.php';
?>

    <!-- Botón hamburguesa -->
    
    <button class="btn-hamburger" id="btnHamburger" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
    
    

    <!-- Overlay para móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="row g-0 layout-wrapper">
        <div class="sidebar-col" id="sidebarCol">
            <?php require_once'public/menu.php'; ?>
        </div>
        <div class="content-col" id="contentCol">
            <?php require_once'core/router.php'; ?>
            <script>var currentPage = '<?= htmlspecialchars($page) ?>';</script>
        </div>
    </div>


<?php
    require_once'public/footer.php';
?>
