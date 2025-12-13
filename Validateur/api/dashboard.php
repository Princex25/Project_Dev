<?php

require_once __DIR__ . '/../config.php';

$pdo = getDB();
$user = getCurrentUser();
$stats = getStatistiques();
$teamId = $user['equipe_id'] ?? null;

function getAdminId($pdo) {
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'Administrateur' ORDER BY id LIMIT 1");
    $row = $stmt->fetch();
    return $row['id'] ?? null;
}

function getNotificationRecente($pdo, $teamId) {
    $stmt = $pdo->prepare("
        SELECT d.*, u.nom_complet as demandeur_nom, t.nom as type_nom, d.description as description_courte
        FROM demandes d
        JOIN users u ON d.demandeur_id = u.id
        JOIN types_besoins t ON d.type_id = t.id
        WHERE d.statut = 'En attente' AND u.equipe_id = ?
        ORDER BY d.date_creation DESC
        LIMIT 1
    ");
    $stmt->execute([$teamId]);
    return $stmt->fetch();
}

function traiterAction($pdo, $userId, $teamId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['demande_id'])) {
        $demandeId = intval($_POST['demande_id']);
        $action = $_POST['action'];
        
        if ($action === 'valider') {
            $stmt = $pdo->prepare("UPDATE demandes SET statut = 'Validée', validateur_id = ?, date_traitement = NOW() WHERE id = ?");
            $stmt->execute([$userId, $demandeId]);

            $stmtDemande = $pdo->prepare("SELECT demandeur_id, description FROM demandes WHERE id = ?");
            $stmtDemande->execute([$demandeId]);
            $demande = $stmtDemande->fetch();
            if ($demande) {
                createNotification($demande['demandeur_id'], "Votre demande a été validée: " . substr($demande['description'], 0, 50), 'success', $demandeId);
            }
        } elseif ($action === 'rejeter') {
            $stmt = $pdo->prepare("UPDATE demandes SET statut = 'Rejetée', validateur_id = ?, date_traitement = NOW() WHERE id = ?");
            $stmt->execute([$userId, $demandeId]);

            $stmtDemande = $pdo->prepare("SELECT demandeur_id, description FROM demandes WHERE id = ?");
            $stmtDemande->execute([$demandeId]);
            $demande = $stmtDemande->fetch();
            if ($demande) {
                createNotification($demande['demandeur_id'], "Votre demande a été rejetée: " . substr($demande['description'], 0, 50), 'error', $demandeId);
            }
        } elseif ($action === 'transmettre') {
            $stmt = $pdo->prepare("UPDATE demandes SET statut = 'En cours de validation', validateur_id = ?, date_traitement = NOW() WHERE id = ?");
            $stmt->execute([$userId, $demandeId]);

            $adminId = getAdminId($pdo);
            if ($adminId) {
                createNotification($adminId, "Demande #$demandeId transmise par un validateur", 'info', $demandeId);
            }
        }
        
        header('Location: ../index.php');
        exit();
    }
}

function getDemandesEnAttente($pdo, $filters, $teamId) {
    $search = $filters['search'] ?? '';
    $typeFilter = $filters['type'] ?? '';
    $demandeurFilter = $filters['demandeur'] ?? '';
    $dateFilter = $filters['date'] ?? '';

    $sql = "
        SELECT d.*, u.nom_complet as demandeur_nom, t.nom as type_nom, d.description as description_courte
        FROM demandes d
        JOIN users u ON d.demandeur_id = u.id
        JOIN types_besoins t ON d.type_id = t.id
        WHERE d.statut = 'En attente' AND u.equipe_id = ?
    ";

    $params = [$teamId];

    if ($search) {
        $sql .= " AND (u.nom_complet LIKE ? OR d.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($typeFilter) {
        $sql .= " AND t.id = ?";
        $params[] = $typeFilter;
    }

    if ($demandeurFilter) {
        $sql .= " AND u.id = ?";
        $params[] = $demandeurFilter;
    }

    if ($dateFilter) {
        $sql .= " AND DATE(d.date_creation) = ?";
        $params[] = $dateFilter;
    }

    $sql .= " ORDER BY d.date_creation DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getDemandeurs($pdo, $teamId) {
    $stmt = $pdo->prepare("SELECT DISTINCT u.id, u.nom_complet FROM users u JOIN demandes d ON u.id = d.demandeur_id WHERE u.role = 'Demandeur' AND u.equipe_id = ?");
    $stmt->execute([$teamId]);
    return $stmt->fetchAll();
}

function getStatsGraphiques($pdo, $teamId) {
    $base = " FROM demandes d JOIN users u ON d.demandeur_id = u.id WHERE u.equipe_id = ?";

    $stmt = $pdo->prepare("SELECT COUNT(*)".$base." AND (d.statut = 'Validée' OR d.statut = 'Traitée')");
    $stmt->execute([$teamId]);
    $demandesTraitees = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*)".$base." AND d.statut = 'Rejetée'");
    $stmt->execute([$teamId]);
    $demandesRejetees = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*)".$base." AND d.statut = 'En attente'");
    $stmt->execute([$teamId]);
    $demandesNonTraitees = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*)".$base);
    $stmt->execute([$teamId]);
    $totalDemandes = $stmt->fetchColumn();

    return [
        'traitees' => $demandesTraitees,
        'rejetees' => $demandesRejetees,
        'non_traitees' => $demandesNonTraitees,
        'total' => $totalDemandes
    ];
}

traiterAction($pdo, $_SESSION['user_id'], $teamId);

$notificationRecente = $teamId ? getNotificationRecente($pdo, $teamId) : null;
$filters = [
    'search' => $_GET['search'] ?? '',
    'type' => $_GET['type'] ?? '',
    'demandeur' => $_GET['demandeur'] ?? '',
    'date' => $_GET['date'] ?? ''
];
$demandesEnAttente = $teamId ? getDemandesEnAttente($pdo, $filters, $teamId) : [];
$types = getTypesdemandes();
$demandeurs = $teamId ? getDemandeurs($pdo, $teamId) : [];
$statsGraphiques = $teamId ? getStatsGraphiques($pdo, $teamId) : ['traitees'=>0,'rejetees'=>0,'non_traitees'=>0,'total'=>0];
?>
