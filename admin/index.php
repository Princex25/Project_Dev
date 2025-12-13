<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="app-container">
<button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
<aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                
                <h2>Admin Panel</h2>
            </div>
            <nav class="sidebar-menu">
                <a href="index.php" class="menu-item active">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <a href="users.php" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Gestion Utilisateurs</span>
                </a>
                <a href="types.php" class="menu-item">
                    <i class="fas fa-tags"></i>
                    <span>Gérer Types</span>
                </a>
                <a href="logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </nav>
        </aside>
<main class="main-content">
<header class="header">
                <div class="header-title">
                    <h1>Tableau de Bord Administrateur</h1>
                </div>
                <div class="header-actions">
<div class="notification-bell" id="notificationBell">
                        <i class="fas fa-bell"></i>
                        <span class="badge" id="notificationCount">2</span>
<div class="notifications-panel" id="notificationsPanel">
                            <div class="notifications-header">
                                <h3>Notifications <span class="badge bg-danger" id="notifBadge">2</span></h3>
                                <span class="mark-read" onclick="markAllAsRead()">Tout lire</span>
                            </div>
                            <div class="notifications-list" id="notificationsList">
</div>
                        </div>
                    </div>
<div class="user-dropdown">
                        <div class="user-dropdown-toggle" id="userDropdownToggle">
                            <img src="assets/images/avatar.png" alt="Avatar" class="avatar" onerror="this.src='https://ui-avatars.com/api/?name=Ahmed&background=00d4ff&color=fff'">
                            <span id="currentUserName">Ahmed</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="user-dropdown-menu" id="userDropdownMenu">
                            <a href="profile.php" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                <span>Mon Profil</span>
                            </a>
                            <a href="#" class="dropdown-item" onclick="openAccountModal()">
                                <i class="fas fa-cog"></i>
                                <span>Gérer le Compte</span>
                            </a>
                            <a href="logout.php" class="dropdown-item danger">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Déconnexion</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>
<div class="grid-3 mb-3">
                <div class="stat-card cyan">
                    <div class="stat-label">Demandes Totales</div>
                    <div class="stat-value" id="totalDemandes">0</div>
                    <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
                </div>
                <div class="stat-card cyan">
                    <div class="stat-label">À Affecter</div>
                    <div class="stat-value" id="toAssign">0</div>
                    <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <a href="assignment.php" class="stat-action">Assigner</a>
                </div>
                <div class="stat-card red">
                    <div class="stat-label">En Retard</div>
                    <div class="stat-value" id="lateRequests">0</div>
                    <div class="stat-icon"><i class="fas fa-fire"></i></div>
                    <a href="prioritization.php" class="stat-action red">Prioriser</a>
                </div>
            </div>
<div class="charts-row">
                <div class="card chart-card">
                    <h3 class="chart-title">Répartition par Type de Besoin</h3>
                    <canvas id="typeChart" height="200"></canvas>
                    <div class="chart-legend" id="typeLegend"></div>
                </div>
                <div class="card chart-card">
                    <h3 class="chart-title">Statut des Demandes</h3>
                    <canvas id="statusChart" height="200"></canvas>
                    <div class="chart-legend" id="statusLegend"></div>
                </div>
            </div>
<div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Demandes Récentes</h3>
                    <div class="table-filters">
                        <div class="search-input">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" placeholder="Search" id="searchInput">
                        </div>
                        <select class="form-control form-select" id="filterType" style="width: 150px;">
                            <option value="">Tous</option>
                        </select>
                        <select class="form-control form-select" id="filterStatus" style="width: 150px;">
                            <option value="">Tous</option>
                            <option value="En attente">En attente</option>
                            <option value="Validée">Validée</option>
                            <option value="Rejetée">Rejetée</option>
                            <option value="En cours">En cours</option>
                        </select>
                        <select class="form-control form-select" id="filterService" style="width: 150px;">
                            <option value="">Tous</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Demander</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Service</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="demandesTableBody">
</tbody>
                    </table>
                </div>
                <div class="pagination" id="pagination"></div>
            </div>
        </main>
    </div>
<div class="modal-overlay" id="accountModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Gérer le Compte</h3>
                <button class="modal-close" onclick="closeAccountModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="accountForm">
                    <div class="form-group">
                        <label class="form-label">Nom Complet <span class="required">*</span></label>
                        <input type="text" class="form-control" id="accountName" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" class="form-control" id="accountEmail" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Rôle</label>
                        <input type="text" class="form-control" id="accountRole" readonly>
                        <div class="form-hint">Le rôle ne peut être modifié que par un administrateur</div>
                    </div>
                    <h4 class="mt-3 mb-2">Changer le Mot de Passe</h4>
                    <div class="form-group">
                        <label class="form-label">Mot de Passe Actuel</label>
                        <input type="password" class="form-control" id="currentPassword" placeholder="Entrez votre mot de passe actuel">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nouveau Mot de Passe</label>
                        <input type="password" class="form-control" id="newPassword" placeholder="Minimum 6 caractères">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmer le Nouveau Mot de Passe</label>
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Confirmez votre nouveau mot de passe">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="saveAccountChanges()">Sauvegarder les Modifications</button>
                <button class="btn btn-secondary" onclick="closeAccountModal()">Annuler</button>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>
