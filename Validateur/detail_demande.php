<?php

require_once 'api/detail_demande.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Demande - Validater</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="content" id="mainContent">
            <div class="page-header">
                <a href="index.php" class="back-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Retour au Dashboard
                </a>
            </div>
            
            <h2 class="page-title">Détails de la Demande #<?php echo $demande['id']; ?></h2>
            
            <div class="detail-grid">
<div class="card detail-info-card">
                    <div class="info-row">
                        <div class="info-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <span class="info-label">Demandeur</span>
                                <span class="info-value"><?php echo htmlspecialchars($demande['demandeur_nom']); ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <span class="info-label">Type</span>
                                <span class="info-value"><?php echo htmlspecialchars($demande['type_nom']); ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="#22c55e" stroke-width="2"/>
                                <path d="M12 6V12L16 14" stroke="#22c55e" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <div>
                                <span class="info-label">Priorité</span>
                                <span class="info-value"><?php echo htmlspecialchars($demande['priorite'] ?? 'Normale'); ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke="#22c55e" stroke-width="2"/>
                                <path d="M16 2V6M8 2V6M3 10H21" stroke="#22c55e" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <div>
                                <span class="info-label">Date de création</span>
                                <span class="info-value"><?php echo date('Y-m-d H:i', strtotime($demande['date_creation'])); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
<div class="card detail-status-card">
                    <div class="status-section">
                        <span class="status-label">Statut actuel</span>
                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $demande['statut'])); ?>">
                            <?php echo htmlspecialchars($demande['statut']); ?>
                        </span>
                    </div>
                    <?php if ($demande['budget_estime']): ?>
                        <div class="budget-section">
                            <span class="budget-label">Budget estimé</span>
                            <span class="budget-value"><?php echo htmlspecialchars($demande['budget_estime']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
<div class="card detail-description-card">
                <h3>Description</h3>
                <p><?php echo htmlspecialchars($demande['description']); ?></p>
            </div>
<div class="card detail-justification-card">
                <h3>Détails complémentaires</h3>
                <?php 
                $justification = $demande['justification'] ?? $demande['details_justification'] ?? '';
                $parts = explode('.', $justification, 2);
                ?>
                <p><?php echo htmlspecialchars($parts[0]); ?>.</p>
                <?php if (isset($parts[1]) && !empty(trim($parts[1]))): ?>
                    <h4>Justification</h4>
                    <p><?php echo htmlspecialchars(trim($parts[1])); ?></p>
                <?php endif; ?>
            </div>
<?php if ($demande['statut'] === 'En attente'): ?>
                <div class="detail-actions">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="rejeter">
                        <button type="submit" class="btn btn-danger btn-lg">Rejeter</button>
                    </form>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="valider">
                        <button type="submit" class="btn btn-success btn-lg">Valider</button>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
