<?php

$pageTitle = 'Création Demande';
$currentPage = 'creation';
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

$error = '';
$success = '';

$types = [];
try {
    $stmt = $pdo->query("SELECT id, nom FROM types_besoins ORDER BY nom");
    $types = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Impossible de charger les types de besoins : " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $typeId = isset($_POST['type']) ? intval($_POST['type']) : 0;
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $priorite = isset($_POST['priorite']) ? trim($_POST['priorite']) : '';
    $isDraft = isset($_POST['save_draft']);

    if (!$typeId || !$description || !$priorite) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        try {
            if ($isDraft) {

                $stmt = $pdo->prepare("INSERT INTO brouillons (demandeur_id, type_id, description, priorite) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $typeId, $description, $priorite]);
                $success = 'Demande sauvegardée en brouillon avec succès!';
            } else {

                $stmt = $pdo->prepare("INSERT INTO demandes (demandeur_id, type_id, description, priorite, statut) VALUES (?, ?, ?, ?, 'En attente')");
                $stmt->execute([$_SESSION['user_id'], $typeId, $description, $priorite]);
                $demandeId = $pdo->lastInsertId();

                $stmt = $pdo->prepare("INSERT INTO historique (demande_id, user_id, action, ancien_statut, nouveau_statut, details) VALUES (?, ?, 'Demande créée', NULL, 'En attente', NULL)");
                $stmt->execute([$demandeId, $_SESSION['user_id']]);

                if (isset($_FILES['pieces_jointes']) && !empty($_FILES['pieces_jointes']['name'][0])) {
                    $uploadDir = '../uploads/demandes/' . $demandeId . '/';
                    
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    foreach ($_FILES['pieces_jointes']['tmp_name'] as $key => $tmpName) {
                        if ($_FILES['pieces_jointes']['error'][$key] === UPLOAD_ERR_OK) {
                            $fileName = basename($_FILES['pieces_jointes']['name'][$key]);
                            $filePath = $uploadDir . $fileName;
                            
                            if (move_uploaded_file($tmpName, $filePath)) {
                                $stmt = $pdo->prepare("INSERT INTO pieces_jointes (demande_id, nom_fichier, chemin_fichier, taille) VALUES (?, ?, ?, ?)");
                                $stmt->execute([$demandeId, $fileName, $filePath, $_FILES['pieces_jointes']['size'][$key]]);
                            }
                        }
                    }
                }

                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, demande_id, message) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $demandeId, "Votre demande #$demandeId a été créée avec succès"]);

                header('Location: mes-demandes.php?success=1');
                exit;
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
            <div class="card">
                <h1 class="mb-4" style="font-size: 1.875rem;">Création d'une Nouvelle Demande</h1>
                
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
                
                <form method="POST" action="" enctype="multipart/form-data" data-validate>
<div class="form-group">
                        <label for="type" class="form-label">
                            Type de besoin <span class="required">*</span>
                        </label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="">Sélectionner un type</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?php echo $t['id']; ?>" <?php echo (isset($typeId) && $typeId == $t['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($t['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
<div class="form-group">
                        <label for="description" class="form-label">
                            Description détaillée <span class="required">*</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  class="form-control" 
                                  rows="6" 
                                  placeholder="Décrivez votre demande en détail..."
                                  required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                    </div>
<div class="form-group">
                            <label for="priorite" class="form-label">
                                Priorité <span class="required">*</span>
                            </label>
                            <select id="priorite" name="priorite" class="form-control" required>
                                <option value="">Sélectionner une priorité</option>
                                <?php $prioriteValue = $priorite ?? ''; ?>
                                <?php foreach (['Faible','Normale','Moyenne','Haute','Urgente'] as $p): ?>
                                    <option value="<?php echo $p; ?>" <?php echo $prioriteValue === $p ? 'selected' : ''; ?>><?php echo $p; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
<div class="form-group">
                        <label for="piecesJointes" class="form-label">
                            Pièces jointes
                        </label>
                        <div class="file-input-wrapper">
                            <input type="file" 
                                   id="piecesJointes" 
                                   name="pieces_jointes[]" 
                                   multiple 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                        </div>
                        <div id="fileList" class="file-list"></div>
                    </div>
<div class="actions-row mt-4">
                        <button type="submit" name="submit" class="btn btn-primary">
                            Envoyer
                        </button>
                        <button type="submit" name="save_draft" class="btn btn-secondary">
                            Sauvegarder en brouillon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>
