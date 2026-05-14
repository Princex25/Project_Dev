<?php

require_once 'api/dashboard.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Validater</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="content" id="mainContent">
            <div class="dashboard-grid">
<div class="card stats-card">
                    <h3 class="card-title">Statistiques d'équipe</h3>
                    <div class="chart-legend">
                        <span class="legend-item">
                            <span class="legend-dot blue"></span>
                            Demande Traiter
                        </span>
                    </div>
                    <div class="chart-container">
                        <canvas id="statsChart"></canvas>
                    </div>
                    <div class="chart-legend-bottom">
                        <span class="legend-item">
                            <span class="legend-dot green"></span>
                            Valider
                        </span>
                        <span class="legend-item">
                            <span class="legend-dot red"></span>
                            Rejeter
                        </span>
                    </div>
                </div>
<div class="card donut-card">
                    <h3 class="card-title">Demande non Traiter</h3>
                    <div class="donut-container">
                        <canvas id="donutChart"></canvas>
                        <div class="donut-center">
                            <span class="donut-value"><?php echo $statsGraphiques['non_traitees']; ?></span>
                            <span class="donut-label">Demande</span>
                        </div>
                    </div>
                    <div class="chart-legend-bottom">
                        <span class="legend-item">
                            <span class="legend-dot gray"></span>
                            Expirer
                        </span>
                        <span class="legend-item">
                            <span class="legend-dot cyan"></span>
                            En attente
                        </span>
                    </div>
                </div>
<div class="card notification-card">
                    <h3 class="card-title">Notifications récentes</h3>
                    <?php if ($notificationRecente): ?>
                        <div class="notification-content">
                            <p class="notification-title">Nouvelle demande de <?php echo htmlspecialchars($notificationRecente['demandeur_nom']); ?></p>
                            <p class="notification-desc">Description : <?php echo htmlspecialchars($notificationRecente['description']); ?></p>
                            <div class="notification-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="demande_id" value="<?php echo $notificationRecente['id']; ?>">
                                    <input type="hidden" name="action" value="valider">
                                    <button type="submit" class="btn btn-success">Valide</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="demande_id" value="<?php echo $notificationRecente['id']; ?>">
                                    <input type="hidden" name="action" value="rejeter">
                                    <button type="submit" class="btn btn-danger">Rejeter</button>
                                </form>
                            </div>
                            <p class="notification-id">ID demande : <?php echo $notificationRecente['id']; ?></p>
                        </div>
                    <?php else: ?>
                        <p class="no-notification">Aucune notification récente</p>
                    <?php endif; ?>
                </div>
            </div>
<div class="card table-card">
                <h3 class="card-title">Demandes en Attente de validation</h3>
                
                <div class="table-filters">
                    <form method="GET" class="filters-form">
                        <div class="search-box">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($filters['search']); ?>">
                        </div>
                        
                        <select name="type" class="filter-select">
                            <option value="">Type</option>
                            <?php foreach ($types as $type): ?>
                                <option value="<?php echo $type['id']; ?>" <?php echo $filters['type'] == $type['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select name="demandeur" class="filter-select">
                            <option value="">Demandeur</option>
                            <?php foreach ($demandeurs as $dem): ?>
                                <option value="<?php echo $dem['id']; ?>" <?php echo $filters['demandeur'] == $dem['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dem['nom_complet']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <input type="date" name="date" class="filter-date" value="<?php echo htmlspecialchars($filters['date']); ?>">
                        
                        <button type="submit" class="btn btn-filter">Filtrer</button>
                    </form>
                </div>
                
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Demander</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Urgence</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($demandesEnAttente as $demande): ?>
                                <tr>
                                    <td><?php echo $demande['id']; ?></td>
                                    <td><?php echo htmlspecialchars($demande['demandeur_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($demande['type_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($demande['description']); ?></td>
                                    <td>
                                        <div class="status-dropdown">
                                            <select class="status-select status-<?php echo strtolower(str_replace(' ', '-', $demande['statut'])); ?>" 
                                                    data-demande-id="<?php echo $demande['id']; ?>"
                                                    onchange="updateStatus(this)">
                                                <option value="Validée" <?php echo $demande['statut'] === 'Validée' ? 'selected' : ''; ?>>Validée</option>
                                                <option value="En attente" <?php echo $demande['statut'] === 'En attente' ? 'selected' : ''; ?>>En attente</option>
                                                <option value="Rejetée" <?php echo $demande['statut'] === 'Rejetée' ? 'selected' : ''; ?>>Rejetée</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($demande['priorite'] ?? 'Normale'); ?></td>
                                    <td>
                                        <a href="detail_demande.php?id=<?php echo $demande['id']; ?>" class="btn btn-details">Voir Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
<div class="pagination-container">
                    <div class="pagination">
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <button class="page-btn">4</button>
                        <span class="page-dots">...</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>

        const demandesTraitees = <?php echo $statsGraphiques['traitees']; ?>;
        const demandesRejetees = <?php echo $statsGraphiques['rejetees']; ?>;
        const demandesNonTraitees = <?php echo $statsGraphiques['non_traitees']; ?>;

        initCharts(demandesTraitees, demandesRejetees, demandesNonTraitees);
    </script>
</body>
</html>
