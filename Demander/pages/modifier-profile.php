<?php

$pageTitle = 'Modifier Profil';
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
$userEmail = $user['email'] ?? '';
$userRole = $user['role'] ?? '';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (!$nom || !$email) {
        $error = 'Le nom et l\'email sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'L\'adresse email n\'est pas valide.';
    } elseif ($newPassword && $newPassword !== $confirmPassword) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif ($newPassword && strlen($newPassword) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } else {
        try {

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            
            if ($stmt->fetch()) {
                $error = 'Cette adresse email est déjà utilisée.';
            } else {

                if ($newPassword) {

                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET nom_complet = ?, email = ?, mot_de_passe = ? WHERE id = ?");
                    $stmt->execute([$nom, $email, $hashedPassword, $_SESSION['user_id']]);
                } else {

                    $stmt = $pdo->prepare("UPDATE users SET nom_complet = ?, email = ? WHERE id = ?");
                    $stmt->execute([$nom, $email, $_SESSION['user_id']]);
                }

                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_email'] = $email;
                
                $success = 'Profil mis à jour avec succès!';

                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
                $userNom = $user['nom_complet'] ?? '';
                $userEmail = $user['email'] ?? '';
                $userRole = $user['role'] ?? '';
            }
        } catch (PDOException $e) {
            $error = 'Une erreur est survenue: ' . $e->getMessage();
        }
    }
}

require_once '../includes/sidebar.php';
?>
<main class="main-content">
        <div style="max-width: 900px; margin: 0 auto;">
<a href="profile.php" class="back-link">
                <i class="bi bi-arrow-left"></i>
                Retour au profil
            </a>
            
            <div class="card" style="padding: 0; overflow: hidden;">
<div class="profile-header">
                    <div class="profile-info">
                        <img src="<?php echo $basePath; ?>assets/images/avatar.svg" 
                             alt="<?php echo htmlspecialchars($userNom ?: 'Avatar utilisateur'); ?>" 
                             class="profile-avatar">
                        <div class="profile-details">
                            <h1>Modifier le profil</h1>
                            <p class="role"><?php echo htmlspecialchars($userRole); ?></p>
                        </div>
                    </div>
                </div>
<div class="detail-body">
                    <?php if ($error): ?>
                    <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.2); border: 1px solid var(--color-red); padding: 1rem; border-radius: var(--radius-lg); color: var(--color-red);">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success mb-4" style="background: rgba(34, 197, 94, 0.2); border: 1px solid var(--color-green-500); padding: 1rem; border-radius: var(--radius-lg); color: var(--color-green);">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" data-validate>
<div class="form-group">
                            <label for="nom" class="form-label">
                                <i class="bi bi-person"></i>
                                Nom complet <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   id="nom" 
                                   name="nom" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($userNom); ?>"
                                   required>
                        </div>
<div class="form-group">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i>
                                Adresse email <span class="required">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($userEmail); ?>"
                                   required>
                        </div>
                        
                        <hr style="border-color: var(--border-gray); margin: 2rem 0;">
                        
                        <h3 class="mb-4">Changer le mot de passe (optionnel)</h3>
<div class="form-group">
                            <label for="new_password" class="form-label">
                                <i class="bi bi-lock"></i>
                                Nouveau mot de passe
                            </label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   class="form-control" 
                                   placeholder="Laisser vide pour ne pas modifier">
                        </div>
<div class="form-group">
                            <label for="confirm_password" class="form-label">
                                <i class="bi bi-lock-fill"></i>
                                Confirmer le mot de passe
                            </label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   class="form-control" 
                                   placeholder="Confirmer le nouveau mot de passe">
                        </div>
<div class="actions-row mt-4">
                            <a href="profile.php" class="btn btn-secondary">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
