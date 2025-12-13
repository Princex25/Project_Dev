<?php

$pageTitle = 'Mes Demandes';
$currentPage = 'mes-demandes';
$basePath = '../';

require_once '../includes/header.php';

if (!$pdo) {
    echo '<div style="background: #ff4444; color: white; padding: 20px; margin: 100px 20px; border-radius: 10px; text-align: center;">';
    echo '<h2>Base de données non configurée</h2>';
    echo '<p><a href="../install.php" style="color: #fff; text-decoration: underline;">Cliquez ici pour installer la base de données</a></p>';
    echo '</div>';
    require_once '../includes/footer.php';
    exit;
}

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$typeFilter = isset($_GET['type']) ? trim($_GET['type']) : '';
$statutFilter = isset($_GET['statut']) ? trim($_GET['statut']) : '';

$whereConditions = ["d.demandeur_id = ?"];
$params = [$_SESSION['user_id']];

if ($searchTerm) {
    $whereConditions[] = "(d.description LIKE ? OR t.nom LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
}

if ($typeFilter) {
    $whereConditions[] = "t.nom = ?";
    $params[] = $typeFilter;
}

if ($statutFilter) {
    $whereConditions[] = "d.statut = ?";
    $params[] = $statutFilter;
}

$whereClause = implode(' AND ', $whereConditions);

$countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM demandes d LEFT JOIN types_besoins t ON d.type_id = t.id WHERE $whereClause");
$countStmt->execute($params);
$total = $countStmt->fetch()['total'];
$totalPages = ceil($total / $limit);

$sql = "
    SELECT d.*, t.nom AS type_nom
    FROM demandes d
    LEFT JOIN types_besoins t ON d.type_id = t.id
    WHERE $whereClause
    ORDER BY d.date_creation DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$demandes = $stmt->fetchAll();

require_once '../includes/sidebar.php';
?>
<main class="main-content">
        <div style="max-width: 1400px; margin: 0 auto;">
            <div class="card card-dark">
                <h1 class="mb-4" style="font-size: 1.875rem;">Mes Demandes</h1>
                
                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success mb-4" style="background: rgba(34, 197, 94, 0.2); border: 1px solid var(--color-green-500); padding: 1rem; border-radius: var(--radius-lg); color: var(--color-green);">
                    Demande créée avec succès!
                </div>
                <?php endif; ?>
<form method="GET" action="">
                    <div class="filters-row mb-4">
                        <div class="search-wrapper">
                            <i class="bi bi-search"></i>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Rechercher..." 
                                   value="<?php echo htmlspecialchars($searchTerm); ?>">
                        </div>
                        <select name="type" class="form-control filter-select" onchange="this.form.submit()">
                            <option value="">Tous les types</option>
                            <option value="Matériel" <?php echo $typeFilter === 'Matériel' ? 'selected' : ''; ?>>Matériel</option>
                            <option value="Logiciel" <?php echo $typeFilter === 'Logiciel' ? 'selected' : ''; ?>>Logiciel</option>
                            <option value="Service" <?php echo $typeFilter === 'Service' ? 'selected' : ''; ?>>Service</option>
                            <option value="Autre" <?php echo $typeFilter === 'Autre' ? 'selected' : ''; ?>>Autre</option>
                        </select>
                        <select name="statut" class="form-control filter-select" style="min-width: 200px;" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="Validée" <?php echo $statutFilter === 'Validée' ? 'selected' : ''; ?>>Validée</option>
                            <option value="En cours de validation" <?php echo $statutFilter === 'En cours de validation' ? 'selected' : ''; ?>>En cours de validation</option>
                            <option value="Rejetée" <?php echo $statutFilter === 'Rejetée' ? 'selected' : ''; ?>>Rejetée</option>
                            <option value="En attente" <?php echo $statutFilter === 'En attente' ? 'selected' : ''; ?>>En attente</option>
                            <option value="Traitée" <?php echo $statutFilter === 'Traitée' ? 'selected' : ''; ?>>Traitée</option>
                        </select>
                    </div>
                </form>
<p class="mb-4">
                    <?php echo $total; ?> demande(s) trouvée(s) - Page <?php echo $page; ?> sur <?php echo max(1, $totalPages); ?>
                </p>
<div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Urgence</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($demandes)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-gray" style="padding: 2rem;">
                                    Aucune demande trouvée
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($demandes as $demande): ?>
                                <tr>
                                    <td>#<?php echo $demande['id']; ?></td>
                                    <?php $typeNom = $demande['type_nom'] ?? 'Non défini'; ?>
                                    <td><?php echo htmlspecialchars($typeNom); ?></td>
                                    <td class="truncate"><?php echo htmlspecialchars($demande['description']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo getStatutColor($demande['statut']); ?>">
                                            <?php echo getStatutLabel($demande['statut']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php $priorite = $demande['priorite'] ?? 'Non définie'; ?>
                                        <span class="<?php echo getUrgenceColor($priorite); ?>">
                                            <?php echo htmlspecialchars($priorite); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($demande['date_creation'])); ?></td>
                                    <td>
                                        <a href="demande-details.php?id=<?php echo $demande['id']; ?>" class="btn btn-success btn-sm">
                                            Voir Détails <i class="bi bi-chevron-right"></i>
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
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
