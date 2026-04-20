<nav class="sidebar" id="sidebar">
        <div class="sidebar-nav">
            <a href="<?php echo isset($basePath) ? $basePath : ''; ?>index.php" class="nav-item <?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo isset($basePath) ? $basePath : ''; ?>pages/creation-demande.php" class="nav-item <?php echo ($currentPage ?? '') === 'creation' ? 'active' : ''; ?>">
                <i class="bi bi-person-gear"></i>
                <span>Création Demande</span>
            </a>
            <a href="<?php echo isset($basePath) ? $basePath : ''; ?>pages/mes-demandes.php" class="nav-item <?php echo ($currentPage ?? '') === 'mes-demandes' ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-text"></i>
                <span>Mes Demander</span>
            </a>
            <a href="<?php echo isset($basePath) ? $basePath : ''; ?>pages/logout.php" class="nav-item">
                <i class="bi bi-box-arrow-left"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </nav>
