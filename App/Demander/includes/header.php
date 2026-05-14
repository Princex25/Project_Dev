<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Demander</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo isset($basePath) ? $basePath : ''; ?>css/style.css" rel="stylesheet">
</head>

<body>
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <header class="header">
        <div class="header-title">
            <h1>Dashboard Employé</h1>
        </div>
        <div class="header-actions">
            <div class="notification-bell" id="notificationBell">
                <i class="fas fa-bell"></i>
                <?php
                $unreadCount = countUnreadNotifications($_SESSION['user_id']);
                if ($unreadCount > 0):
                ?>
                    <span class="badge" id="notificationCount"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
                <div class="notifications-panel" id="notificationsPanel">
                    <div class="notifications-header">
                        <h3>Notifications <?php if ($unreadCount > 0): ?><span class="badge bg-danger"><?php echo $unreadCount; ?></span><?php endif; ?></h3>
                        <span class="mark-read" onclick="markAllAsRead()">Tout lire</span>
                    </div>
                    <div class="notifications-list" id="notificationsList">
                        <?php
                        $notifications = getUnreadNotifications($_SESSION['user_id']);
                        if (empty($notifications)):
                        ?>
                            <div class="notification-empty">Aucune notification</div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notif): ?>
                                <div class="notification-item <?php echo !$notif['lu'] ? 'unread' : ''; ?>">
                                    <div class="notification-dot <?php echo !$notif['lu'] ? 'unread' : ''; ?>"></div>
                                    <div class="notification-content">
                                        <p class="notification-text"><?php echo htmlspecialchars($notif['message']); ?></p>
                                        <span class="notification-time">
                                            <?php echo date('d/m/Y H:i', strtotime($notif['date_creation'])); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="user-dropdown">
                <div class="user-dropdown-toggle" id="userDropdownToggle">
                    <img src="<?php echo isset($basePath) ? $basePath : ''; ?>assets/images/avatar.svg" alt="Avatar" class="avatar" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'User'); ?>&background=00d4ff&color=fff'">
                    <span id="currentUserName"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="user-dropdown-menu" id="userDropdownMenu">
                    <a href="<?php echo isset($basePath) ? $basePath : ''; ?>pages/profile.php" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>Mon Profil</span>
                    </a>
                    <a href="<?php echo isset($basePath) ? $basePath : ''; ?>pages/modifier-profile.php" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                    <a href="<?php echo isset($basePath) ? $basePath : ''; ?>pages/logout.php" class="dropdown-item danger">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
    </header>