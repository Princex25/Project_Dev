<?php

$pageTitle = 'Mon Profil';
$currentPage = 'profile';
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

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    echo '<div style="background: #ff4444; color: white; padding: 20px; margin: 100px 20px; border-radius: 10px; text-align: center;">';
    echo '<h2>Utilisateur introuvable</h2>';
    echo '<p>Votre session semble invalide. Merci de vous reconnecter.</p>';
    echo '<p><a href="../logout.php" style="color: #fff; text-decoration: underline;">Se reconnecter</a></p>';
    echo '</div>';
    require_once '../includes/footer.php';
    exit;
}

$userNom = $user['nom_complet'] ?? '';
$userRole = $user['role'] ?? '';
$userEmail = $user['email'] ?? '';
$userId = $user['id'] ?? '';

require_once '../includes/sidebar.php';
?>
<main class="main-content">
        <div style="max-width: 900px; margin: 0 auto;">
<a href="../index.php" class="back-link">
                <i class="bi bi-arrow-left"></i>
                Retour au tableau de bord
            </a>
            
            <div class="card" style="padding: 0; overflow: hidden;">
<div class="profile-header">
                    <div class="profile-info">
                        <img src="<?php echo $basePath; ?>assets/images/avatar.svg" 
                             alt="<?php echo htmlspecialchars($userNom ?: 'Avatar utilisateur'); ?>" 
                             class="profile-avatar">
                        <div class="profile-details">
                            <h1><?php echo htmlspecialchars($userNom); ?></h1>
                            <p class="role"><?php echo htmlspecialchars($userRole); ?></p>
                        </div>
                    </div>
                </div>
<div class="detail-body">
                    <h2 class="mb-4" style="font-size: 1.5rem;">Informations du profil</h2>
<div class="detail-field">
                        <label class="detail-label">
                            <i class="bi bi-person"></i>
                            Nom complet
                        </label>
                        <div class="detail-value">
                            <?php echo htmlspecialchars($userNom); ?>
                        </div>
                    </div>
<div class="detail-field">
                        <label class="detail-label">
                            <i class="bi bi-envelope"></i>
                            Adresse email
                        </label>
                        <div class="detail-value">
                            <?php echo htmlspecialchars($userEmail); ?>
                        </div>
                    </div>
<div class="detail-field">
                        <label class="detail-label">
                            <i class="bi bi-shield"></i>
                            Rôle
                        </label>
                        <div class="detail-value">
                            <span style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; padding: 0.25rem 0.75rem; border-radius: var(--radius-lg); border: 1px solid #60a5fa;">
                                <?php echo htmlspecialchars($userRole); ?>
                            </span>
                        </div>
                    </div>
<div class="detail-field">
                        <label class="detail-label">ID Utilisateur</label>
                        <div class="detail-value">
                            #<?php echo htmlspecialchars((string) $userId); ?>
                        </div>
                    </div>
                </div>
<div class="card-footer">
                    <div class="actions-row">
                        <a href="../index.php" class="btn btn-secondary">
                            Retour
                        </a>
                        <a href="modifier-profile.php" class="btn btn-primary">
                            Modifier le profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
