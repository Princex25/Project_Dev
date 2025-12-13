<?php

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['demande_id']) && isset($_POST['status'])) {
    $demandeId = intval($_POST['demande_id']);
    $status = $_POST['status'];
    
    $validStatuses = ['Valider', 'En attente', 'Rejeter', 'Traitée'];
    
    if (in_array($status, $validStatuses)) {
        $pdo = getConnection();
        $stmt = $pdo->prepare("UPDATE demandes SET statut = ?, validateur_id = ?, date_traitement = NOW() WHERE id = ?");
        $result = $stmt->execute([$status, $_SESSION['user_id'], $demandeId]);
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur base de données']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Statut invalide']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Requête invalide']);
}
?>
