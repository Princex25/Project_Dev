<?php

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
$basePath = '';

require_once 'includes/header.php';

if (!$pdo) {
    echo '<div style="background: #ff4444; color: white; padding: 20px; margin: 100px 20px; border-radius: 10px; text-align: center;">';
    echo '<h2>Base de données non configurée</h2>';
    echo '<p><a href="install.php" style="color: #fff; text-decoration: underline;">Cliquez ici pour installer la base de données</a></p>';
    echo '</div>';
    require_once 'includes/footer.php';
    exit;
}

$tablesExist = true;
try {
    $pdo->query("SELECT 1 FROM demandes LIMIT 1");
} catch (PDOException $e) {
    $tablesExist = false;
}

if (!$tablesExist) {
    echo '<div style="background: #ff4444; color: white; padding: 20px; margin: 100px 20px; border-radius: 10px; text-align: center;">';
    echo '<h2>Tables non créées</h2>';
    echo '<p>Les tables de la base de données n\'existent pas encore.</p>';
    echo '<p><a href="install.php" style="color: #fff; text-decoration: underline; font-size: 1.2rem;">👉 Cliquez ici pour installer la base de données</a></p>';
    echo '</div>';
    require_once 'includes/footer.php';
    exit;
}

$stats = getStatistiques($pdo, $_SESSION['user_id']);
$statsUrgence = getStatistiquesParUrgence($pdo, $_SESSION['user_id']);

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 7;
$offset = ($page - 1) * $limit;

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$typeFilter = isset($_GET['type']) ? trim($_GET['type']) : '';
$statutFilter = isset($_GET['statut']) ? trim($_GET['statut']) : '';

$whereConditions = ["d.demandeur_id = ?"];
$params = [$_SESSION['user_id']];

if ($searchTerm) {
    $whereConditions[] = "d.description LIKE ?";
    $params[] = "%$searchTerm%";
}

if ($typeFilter) {
    $whereConditions[] = "d.type_id = ?";
    $params[] = $typeFilter;
}

if ($statutFilter) {
    $whereConditions[] = "d.statut = ?";
    $params[] = $statutFilter;
}

$whereClause = implode(' AND ', $whereConditions);

$countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM demandes d WHERE $whereClause");
$countStmt->execute($params);
$total = $countStmt->fetch()['total'];
$totalPages = ceil($total / $limit);

$sql = "SELECT d.*, t.nom as type_nom 
        FROM demandes d 
        LEFT JOIN types_besoins t ON d.type_id = t.id 
        WHERE $whereClause 
        ORDER BY d.date_creation DESC 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$demandes = $stmt->fetchAll();

$recentNotifs = getUnreadNotifications($_SESSION['user_id'], 3);

$typesStmt = $pdo->query("SELECT id, nom FROM types_besoins ORDER BY nom");
$typesBesoins = $typesStmt->fetchAll();

require_once 'includes/sidebar.php';
?>
<main class="main-content">
<div class="stats-grid">
<div class="card stat-card">
                <h3 class="stat-title">Demandes Totales</h3>
                <p class="stat-value cyan"><?php echo $stats['total']; ?></p>
                <div class="stat-bar cyan"></div>
                <a href="pages/mes-demandes.php" class="stat-icon-btn" title="Voir tout">
                    <i class="bi bi-folder2-open"></i>
                </a>
            </div>
<div class="card stat-card">
                <h3 class="stat-title">Valider</h3>
                <p class="stat-value green"><?php echo $stats['validees']; ?></p>
                <div class="stat-bar green"></div>
                <a href="pages/mes-demandes.php?statut=Validée" class="stat-icon-btn" title="Voir validées">
                    <i class="bi bi-folder2-open"></i>
                </a>
            </div>
<div class="card stat-card">
                <h3 class="stat-title">En Attente</h3>
                <p class="stat-value red"><?php echo $stats['en_attente']; ?></p>
                <div class="stat-bar red"></div>
                <a href="pages/mes-demandes.php?statut=En attente" class="stat-icon-btn" title="Voir en attente">
                    <i class="bi bi-folder2-open"></i>
                </a>
            </div>
        </div>
<div class="dashboard-grid">
<div class="card card-dark">
                <h2 class="mb-4">Historique des Demande</h2>
<form method="GET" action="">
                    <div class="filters-row">
                        <div class="search-wrapper">
                            <i class="bi bi-search"></i>
                            <input type="text" 
                                   name="search" 
                                   id="searchInput"
                                   class="form-control" 
                                   placeholder="Search" 
                                   value="<?php echo htmlspecialchars($searchTerm); ?>">
                        </div>
                        <select name="type" id="typeFilter" class="form-control filter-select" onchange="this.form.submit()">
                            <option value="">Type</option>
                            <?php foreach ($typesBesoins as $type): ?>
                            <option value="<?php echo $type['id']; ?>" <?php echo $typeFilter == $type['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($type['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="statut" id="statusFilter" class="form-control filter-select" onchange="this.form.submit()">
                            <option value="">Status</option>
                            <option value="Validée" <?php echo $statutFilter === 'Validée' ? 'selected' : ''; ?>>Valider</option>
                            <option value="En cours de validation" <?php echo $statutFilter === 'En cours de validation' ? 'selected' : ''; ?>>En attente</option>
                            <option value="Rejetée" <?php echo $statutFilter === 'Rejetée' ? 'selected' : ''; ?>>Rejeter</option>
                            <option value="En attente" <?php echo $statutFilter === 'En attente' ? 'selected' : ''; ?>>En attente</option>
                            <option value="Traitée" <?php echo $statutFilter === 'Traitée' ? 'selected' : ''; ?>>Traitée</option>
                        </select>
                    </div>
                </form>
<div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Urgence</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($demandes)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-gray" style="padding: 2rem;">
                                    Aucune demande trouvée
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($demandes as $demande): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($demande['type_nom'] ?? 'N/A'); ?></td>
                                    <td class="truncate"><?php echo htmlspecialchars($demande['description']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo getStatutColor($demande['statut']); ?>">
                                            <?php echo getStatutLabel($demande['statut']); ?>
                                            <i class="bi bi-chevron-down"></i>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($demande['priorite'] ?? 'Normale'); ?></td>
                                    <td>
                                        <a href="pages/demande-details.php?id=<?php echo $demande['id']; ?>" class="btn btn-success btn-sm">
                                            Voir Details <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
<?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <a href="?page=<?php echo max(1, $page - 1); ?>&search=<?php echo urlencode($searchTerm); ?>&type=<?php echo urlencode($typeFilter); ?>&statut=<?php echo urlencode($statutFilter); ?>" 
                       class="pagination-btn <?php echo $page <= 1 ? 'disabled' : ''; ?>"
                       <?php echo $page <= 1 ? 'aria-disabled="true"' : ''; ?>>
                        <i class="bi bi-chevron-left"></i> Précédent
                    </a>
                    
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    if ($startPage > 1): ?>
                        <a href="?page=1&search=<?php echo urlencode($searchTerm); ?>&type=<?php echo urlencode($typeFilter); ?>&statut=<?php echo urlencode($statutFilter); ?>" class="page-number">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="page-ellipsis">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchTerm); ?>&type=<?php echo urlencode($typeFilter); ?>&statut=<?php echo urlencode($statutFilter); ?>" 
                           class="page-number <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <span class="page-ellipsis">...</span>
                        <?php endif; ?>
                        <a href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($searchTerm); ?>&type=<?php echo urlencode($typeFilter); ?>&statut=<?php echo urlencode($statutFilter); ?>" class="page-number"><?php echo $totalPages; ?></a>
                    <?php endif; ?>
                    
                    <a href="?page=<?php echo min($totalPages, $page + 1); ?>&search=<?php echo urlencode($searchTerm); ?>&type=<?php echo urlencode($typeFilter); ?>&statut=<?php echo urlencode($statutFilter); ?>" 
                       class="pagination-btn <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"
                       <?php echo $page >= $totalPages ? 'aria-disabled="true"' : ''; ?>>
                        Suivant <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
<div class="dashboard-sidebar">
<div class="card card-white stats-chart-card">
                    <h3 class="mb-3" style="color: #000;">Statistiques de Demande</h3>
                    <?php
                    $maxValue = max($statsUrgence['faible'], $statsUrgence['moyenne'], $statsUrgence['urgente'], 1);
                    ?>
                    <div class="chart-container">
                        <div class="chart-bar-wrapper">
                            <span class="chart-value"><?php echo $statsUrgence['faible']; ?></span>
                            <div class="chart-bar cyan" style="height: <?php echo ($statsUrgence['faible'] / $maxValue) * 200; ?>px;"></div>
                            <span class="chart-label">Faible</span>
                        </div>
                        <div class="chart-bar-wrapper">
                            <span class="chart-value"><?php echo $statsUrgence['moyenne']; ?></span>
                            <div class="chart-bar green" style="height: <?php echo ($statsUrgence['moyenne'] / $maxValue) * 200; ?>px;"></div>
                            <span class="chart-label">Moyenne</span>
                        </div>
                        <div class="chart-bar-wrapper">
                            <span class="chart-value"><?php echo $statsUrgence['urgente']; ?></span>
                            <div class="chart-bar orange" style="height: <?php echo ($statsUrgence['urgente'] / $maxValue) * 200; ?>px;"></div>
                            <span class="chart-label">Urgent</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require_once 'includes/footer.php'; ?>
