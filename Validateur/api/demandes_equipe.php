<?php

require_once __DIR__ . '/../config.php';
requireLogin();

$pdo = getConnection();
$user = getCurrentUser();
$teamId = $user['equipe_id'] ?? null;

function getAdminId($pdo) {
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'Administrateur' ORDER BY id LIMIT 1");
    $row = $stmt->fetch();
    return $row['id'] ?? null;
}

function traiterChangementStatut($pdo, $userId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['demande_id'])) {
        $demandeId = intval($_POST['demande_id']);
        $newStatus = $_POST['action'];

        $statusMap = [
            'Valider' => 'Validée',
            'Rejeter' => 'Rejetée',
            'En attente' => 'En attente',
            'Traitée' => 'Traitée'
        ];
        $validActions = array_merge(array_keys($statusMap), ['transmettre']);

        if (in_array($newStatus, $validActions)) {
            if ($newStatus === 'transmettre') {
                $stmt = $pdo->prepare("UPDATE demandes SET statut = 'En cours de validation', validateur_id = ?, date_traitement = NOW() WHERE id = ?");
                $stmt->execute([$userId, $demandeId]);
                $adminId = getAdminId($pdo);
                if ($adminId) {
                    createNotification($adminId, "Demande #$demandeId transmise par un validateur", 'info', $demandeId);
                }
            } else {
                $statutValue = $statusMap[$newStatus] ?? 'En attente';
                $stmt = $pdo->prepare("UPDATE demandes SET statut = ?, validateur_id = ?, date_traitement = NOW() WHERE id = ?");
                $stmt->execute([$statutValue, $userId, $demandeId]);
            }
        }
        
        header('Location: demandes_equipe.php');
        exit();
    }
}

function getToutesDemandes($pdo, $filters, $teamId) {
    $search = $filters['search'] ?? '';
    $statutFilter = $filters['statut'] ?? '';
    $demandeurFilter = $filters['demandeur'] ?? '';
    $dateFilter = $filters['date'] ?? '';

    $sql = "
        SELECT d.*, u.nom_complet as demandeur_nom, t.nom as type_nom
        FROM demandes d
        JOIN users u ON d.demandeur_id = u.id
        JOIN types_besoins t ON d.type_id = t.id
        WHERE u.equipe_id = ?
    ";

    $params = [$teamId];

    if ($search) {
        $sql .= " AND (u.nom_complet LIKE ? OR d.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($statutFilter) {
        $sql .= " AND d.statut = ?";
        $params[] = $statutFilter;
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
    $stmt = $pdo->prepare("SELECT DISTINCT u.id, u.nom_complet FROM users u JOIN demandes d ON u.id = d.demandeur_id WHERE u.equipe_id = ? ORDER BY u.nom_complet");
    $stmt->execute([$teamId]);
    return $stmt->fetchAll();
}

traiterChangementStatut($pdo, $_SESSION['user_id']);

$filters = [
    'search' => $_GET['search'] ?? '',
    'statut' => $_GET['statut'] ?? '',
    'demandeur' => $_GET['demandeur'] ?? '',
    'date' => $_GET['date'] ?? ''
];
$demandes = $teamId ? getToutesDemandes($pdo, $filters, $teamId) : [];
$demandeurs = $teamId ? getDemandeurs($pdo, $teamId) : [];
?>
