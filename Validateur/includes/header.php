<?php
$user = getCurrentUser();
$notificationsCount = countUnreadNotifications(getCurrentUserId());
$notifications = getUnreadNotifications(getCurrentUserId());
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>
<header class="main-header">
    <div class="header-title">
        <h1>Dashboard Validateur</h1>
    </div>
    <div class="header-actions">
<div class="notification-bell" id="notificationBell">
            <i class="fas fa-bell"></i>
            <?php if ($notificationsCount > 0): ?>
                <span class="badge" id="notificationCount"><?php echo $notificationsCount; ?></span>
            <?php endif; ?>
<div class="notifications-panel" id="notificationsPanel">
                <div class="notifications-header">
                    <h3>Notifications <?php if ($notificationsCount > 0): ?><span class="badge bg-danger"><?php echo $notificationsCount; ?></span><?php endif; ?></h3>
                    <span class="mark-read" onclick="markAllAsRead()">Tout lire</span>
                </div>
                <div class="notifications-list" id="notificationsList">
                    <?php if (empty($notifications)): ?>
                        <div class="notification-empty">Aucune notification</div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                            <a href="detail_demande.php?id=<?php echo $notif['demande_id']; ?>" class="notification-item <?php echo !$notif['lu'] ? 'unread' : ''; ?>">
                                <div class="notification-dot <?php echo !$notif['lu'] ? 'unread' : ''; ?>"></div>
                                <div class="notification-content">
                                    <p class="notification-text"><?php echo htmlspecialchars($notif['message']); ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<div class="user-dropdown">
            <div class="user-dropdown-toggle" id="userDropdownToggle">
                <?php if ($user && $user['avatar'] && $user['avatar'] !== 'default-avatar.png'): ?>
                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="avatar" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($user['nom_complet'] ?? 'User'); ?>&background=00d4ff&color=fff'">
                <?php else: ?>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['nom_complet'] ?? 'User'); ?>&background=00d4ff&color=fff" alt="Avatar" class="avatar">
                <?php endif; ?>
                <span id="currentUserName"><?php echo $user ? htmlspecialchars($user['nom_complet']) : 'Utilisateur'; ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="user-dropdown-menu" id="userDropdownMenu">
                <a href="profile.php" class="dropdown-item">
                    <i class="fas fa-user"></i>
                    <span>Mon Profil</span>
                </a>
                <a href="modifier_profile.php" class="dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
                <a href="logout.php" class="dropdown-item danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>
    </div>
</header>
