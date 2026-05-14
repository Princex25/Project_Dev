<?php

require_once __DIR__ . '/../config.php';
requireLogin();

$pdo = getConnection();
$user = getCurrentUser();
$types = getTypesdemandes();

$message = '';
$error = '';

function traiterFormulaire($pdo, $userId, $userName) {
    global $error;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }
    
    $type_id = intval($_POST['type_demande']);
    $urgence = $_POST['urgence'];
    $description = trim($_POST['description']);
    $justification = trim($_POST['justification']);
    $budget = trim($_POST['budget']);

    if (empty($type_id) || empty($description)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
        return false;
    }

    $fichier = null;
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileName = time() . '_' . basename($_FILES['fichier']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['fichier']['tmp_name'], $uploadFile)) {
            $fichier = 'uploads/' . $fileName;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO demandes (demandeur_id, type_id, priorite, description, justification, fichier_joint, budget_estime, statut)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'En attente')
    ");
    
    $result = $stmt->execute([
        $userId,
        $type_id,
        $urgence,
        $description,
        $justification,
        $fichier,
        $budget
    ]);
    
    if ($result) {

        $demandeId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, demande_id, message)
            SELECT id, ?, CONCAT('Nouvelle demande de ', ?, ' - Description: ', ?)
            FROM users WHERE role = 'Validateur' LIMIT 1
        ");
        $stmt->execute([$demandeId, $userName, $description]);
        
        header('Location: index.php?success=1');
        exit();
    } else {
        $error = "Erreur lors de la création de la demande.";
        return false;
    }
}

traiterFormulaire($pdo, $_SESSION['user_id'], $user['nom_complet'] ?? 'Utilisateur');
?>
