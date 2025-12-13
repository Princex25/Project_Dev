<?php

require_once __DIR__ . '/../config.php';
requireLogin();

$pdo = getConnection();
$user = getCurrentUser();

function getDetailsDemande($pdo, $demandeId) {
    $stmt = $pdo->prepare("
        SELECT d.*, u.nom_complet as demandeur_nom, t.nom as type_nom
        FROM demandes d
        JOIN users u ON d.demandeur_id = u.id
        JOIN types_besoins t ON d.type_id = t.id
        WHERE d.id = ?
    ");
    $stmt->execute([$demandeId]);
    return $stmt->fetch();
}

function traiterActionDemande($pdo, $demandeId, $userId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'valider') {
            $stmt = $pdo->prepare("UPDATE demandes SET statut = 'Validée', validateur_id = ?, date_traitement = NOW() WHERE id = ?");
            $stmt->execute([$userId, $demandeId]);
        } elseif ($action === 'rejeter') {
            $stmt = $pdo->prepare("UPDATE demandes SET statut = 'Rejetée', validateur_id = ?, date_traitement = NOW() WHERE id = ?");
            $stmt->execute([$userId, $demandeId]);
        }
        
        header('Location: index.php');
        exit();
    }
}

function marquerNotificationLue($pdo, $demandeId, $userId) {
    $stmt = $pdo->prepare("UPDATE notifications SET lu = TRUE WHERE demande_id = ? AND user_id = ?");
    $stmt->execute([$demandeId, $userId]);
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$demandeId = intval($_GET['id']);

$demande = getDetailsDemande($pdo, $demandeId);

if (!$demande) {
    header('Location: index.php');
    exit();
}

traiterActionDemande($pdo, $demandeId, $_SESSION['user_id']);

marquerNotificationLue($pdo, $demandeId, $_SESSION['user_id']);
?>
