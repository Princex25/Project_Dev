<?php

require_once 'api/demandes_equipe.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes de l'Équipe - Validater</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="content" id="mainContent">
            <div class="card table-card full-width">
                <h2 class="page-title">Demandes de l'Équipe</h2>
                
                <div class="table-filters">
                    <form method="GET" class="filters-form">
                        <div class="search-box">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($filters['search']); ?>">
                        </div>
                        
                        <select name="statut" class="filter-select">
                            <option value="">Statut</option>
                            <option value="Valider" <?php echo $filters['statut'] === 'Valider' ? 'selected' : ''; ?>>Valider</option>
                            <option value="En attente" <?php echo $filters['statut'] === 'En attente' ? 'selected' : ''; ?>>En attente</option>
                            <option value="Rejeter" <?php echo $filters['statut'] === 'Rejeter' ? 'selected' : ''; ?>>Rejeter</option>
                            <option value="Traitée" <?php echo $filters['statut'] === 'Traitée' ? 'selected' : ''; ?>>Traitée</option>
                        </select>
                        
                        <select name="demandeur" class="filter-select">
                            <option value="">Demandeur</option>
                            <?php foreach ($demandeurs as $dem): ?>
                                <option value="<?php echo $dem['id']; ?>" <?php echo $filters['demandeur'] == $dem['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dem['nom_complet']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <input type="date" name="date" class="filter-date" placeholder="jj/mm/aaaa" value="<?php echo htmlspecialchars($filters['date']); ?>">
                        
                        <button type="submit" class="btn btn-filter">Filtrer</button>
                    </form>
                </div>
                
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Demandeur</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Statut</th>
                                <th>Priorité</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($demandes as $demande): ?>
                                <tr>
                                    <td><?php echo $demande['id']; ?></td>
                                    <td><?php echo htmlspecialchars($demande['demandeur_nom']); ?></td>
                                    <td class="type-<?php echo strtolower($demande['type_nom']); ?>"><?php echo htmlspecialchars($demande['type_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($demande['description']); ?></td>
                                    <td>
                                        <form method="POST" class="status-form">
                                            <input type="hidden" name="demande_id" value="<?php echo $demande['id']; ?>">
                                            <select name="action" class="status-select status-<?php echo strtolower(str_replace(' ', '-', $demande['statut'])); ?>" onchange="this.form.submit()">
                                                <option value="Valider" <?php echo $demande['statut'] === 'Valider' ? 'selected' : ''; ?>>Valider</option>
                                                <option value="En attente" <?php echo $demande['statut'] === 'En attente' ? 'selected' : ''; ?>>En attente</option>
                                                <option value="Rejeter" <?php echo $demande['statut'] === 'Rejeter' ? 'selected' : ''; ?>>Rejeter</option>
                                                <option value="Traitée" <?php echo $demande['statut'] === 'Traitée' ? 'selected' : ''; ?>>Traitée</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?php echo htmlspecialchars($demande['priorite'] ?? 'Normale'); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($demande['date_creation'])); ?></td>
                                    <td>
                                        <div style="display:flex; gap:5px; align-items:center;">
                                            <a href="detail_demande.php?id=<?php echo $demande['id']; ?>" class="btn btn-view">Voir</a>
                                            <form method="POST" style="margin:0;">
                                                <input type="hidden" name="demande_id" value="<?php echo $demande['id']; ?>">
                                                <input type="hidden" name="action" value="transmettre">
                                                <button type="submit" class="btn btn-secondary btn-sm">Transmettre admin</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
