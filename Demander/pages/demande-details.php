<?php

$pageTitle = 'Détails Demande';
$currentPage = '';
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

$demandeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT d.*, t.nom AS type_nom FROM demandes d LEFT JOIN types_besoins t ON d.type_id = t.id WHERE d.id = ? AND d.demandeur_id = ?");
$stmt->execute([$demandeId, $_SESSION['user_id']]);
$demande = $stmt->fetch();

$piecesJointes = [];
if ($demande) {
    $stmt = $pdo->prepare("SELECT * FROM pieces_jointes WHERE demande_id = ?");
    $stmt->execute([$demandeId]);
    $piecesJointes = $stmt->fetchAll();
}

$historique = [];
if ($demande) {
    $stmt = $pdo->prepare("SELECT * FROM historique WHERE demande_id = ? ORDER BY date_action ASC");
    $stmt->execute([$demandeId]);
    $historique = $stmt->fetchAll();
}

function getUrgenceClass($priorite) {
    switch ($priorite) {
        case 'Urgente':
        case 'Haute': return 'urgence-urgente';
        case 'Moyenne': return 'urgence-moyenne';
        case 'Faible':
        case 'Normale': return 'urgence-faible';
        default: return '';
    }
}

require_once '../includes/sidebar.php';
?>
<main class="main-content">
        <div style="max-width: 900px; margin: 0 auto;">
<a href="mes-demandes.php" class="back-link">
                <i class="bi bi-arrow-left"></i>
                Retour à mes demandes
            </a>
            
            <?php if (!$demande): ?>
            <div class="card text-center" style="padding: 3rem;">
                <h1 class="mb-4">Demande introuvable</h1>
                <a href="mes-demandes.php" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i>
                    Retour à mes demandes
                </a>
            </div>
            <?php else: ?>
            <?php

            $moisFr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
            $dateCreation = new DateTime($demande['date_creation']);
            $dateFormatee = $dateCreation->format('d') . ' ' . $moisFr[$dateCreation->format('n') - 1] . ' ' . $dateCreation->format('Y') . ' à ' . $dateCreation->format('H:i');
            ?>
            <div class="card" style="padding: 0; overflow: hidden;">
<div class="detail-header">
                    <div class="detail-header-content">
                        <div>
                            <h1 style="font-size: 1.875rem; margin-bottom: 0.5rem;">Demande #<?php echo $demande['id']; ?></h1>
                            <p class="text-gray">
                                Créée le <?php echo $dateFormatee; ?>
                            </p>
                        </div>
                        <span class="status-badge <?php echo getStatutColor($demande['statut']); ?>" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                            <?php if ($demande['statut'] === 'Validée'): ?>
                            <i class="bi bi-check-circle"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($demande['statut']); ?>
                        </span>
                    </div>
                </div>
<div class="detail-body">
<div class="detail-grid">
                        <div class="detail-field">
                            <label class="detail-label">
                                <i class="bi bi-file-text"></i>
                                Type de besoin
                            </label>
                            <div class="detail-value">
                                <?php $typeNom = $demande['type_nom'] ?? 'Non défini'; ?>
                                <?php echo htmlspecialchars($typeNom); ?>
                            </div>
                        </div>
                        
                        <div class="detail-field">
                            <label class="detail-label">
                                <i class="bi bi-exclamation-circle"></i>
                                Priorité
                            </label>
                            <?php $priorite = $demande['priorite'] ?? 'Non définie'; ?>
                            <div class="urgence-badge <?php echo getUrgenceClass($priorite); ?>">
                                <?php echo htmlspecialchars($priorite); ?>
                            </div>
                        </div>
                    </div>
<div class="detail-field">
                        <label class="detail-label">Description détaillée</label>
                        <div class="detail-value">
                            <?php echo nl2br(htmlspecialchars($demande['description'])); ?>
                        </div>
                    </div>
<div class="detail-field">
                        <label class="detail-label">Statut actuel</label>
                        <div class="detail-value d-flex align-center justify-between">
                            <span><?php echo htmlspecialchars($demande['statut']); ?></span>
                            <span class="status-badge <?php echo getStatutColor($demande['statut']); ?>" style="border-radius: 20px;">
                                <?php echo htmlspecialchars($demande['statut']); ?>
                            </span>
                        </div>
                    </div>
<div class="detail-field">
                        <label class="detail-label">Pièces jointes</label>
                        <div class="detail-value">
                            <?php if (empty($piecesJointes)): ?>
                            <span class="text-gray">Aucune pièce jointe</span>
                            <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <?php foreach ($piecesJointes as $piece): ?>
                                <a href="<?php echo htmlspecialchars($piece['chemin_fichier']); ?>" 
                                   class="text-blue" 
                                   style="display: flex; align-items: center; gap: 0.5rem; color: #60a5fa;">
                                    <i class="bi bi-file-text"></i>
                                    <?php echo htmlspecialchars($piece['nom_fichier']); ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
<div class="detail-field">
                        <label class="detail-label">
                            <i class="bi bi-calendar3"></i>
                            Historique
                        </label>
                        <div class="detail-value">
                            <div class="timeline">
                                <?php if (empty($historique)): ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot" style="background: var(--color-blue);"></div>
                                    <div class="timeline-content">
                                        <h4>Demande créée</h4>
                                        <p><?php echo date('d/m/Y H:i:s', strtotime($demande['date_creation'])); ?></p>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <?php foreach ($historique as $event): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-dot" style="background: <?php 
                                            echo $event['nouveau_statut'] === 'Validée' ? 'var(--color-cyan)' : 
                                                ($event['nouveau_statut'] === 'Rejetée' ? 'var(--color-red)' : 'var(--color-blue)'); 
                                        ?>;"></div>
                                        <div class="timeline-content">
                                            <h4><?php echo htmlspecialchars($event['action']); ?><?php echo $event['nouveau_statut'] ? ': ' . htmlspecialchars($event['nouveau_statut']) : ''; ?></h4>
                                            <p><?php echo date('d/m/Y H:i:s', strtotime($event['date_action'])); ?></p>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
<div class="card-footer">
                    <div class="actions-row">
                        <a href="mes-demandes.php" class="btn btn-secondary">
                            Retour
                        </a>
                        <?php if ($demande['statut'] === 'En attente'): ?>
                        <a href="modifier-demande.php?id=<?php echo $demande['id']; ?>" class="btn btn-primary">
                            Modifier
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
