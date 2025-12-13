<?php

require_once __DIR__ . '/../shared/config.php';

requireRole([ROLE_VALIDATEUR, ROLE_ADMIN]);

function getCurrentUserValidateur() {
    return getCurrentUser();
}

function getUnreadNotificationsCountValidateur() {
    if (!isLoggedIn()) {
        return 0;
    }
    return countUnreadNotifications(getCurrentUserId());
}

function getNotificationsValidateur($limit = 10) {
    if (!isLoggedIn()) {
        return [];
    }
    
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT n.*, d.description as description_courte 
        FROM notifications n 
        LEFT JOIN demandes d ON n.demande_id = d.id 
        WHERE n.user_id = ? 
        ORDER BY n.date_creation DESC 
        LIMIT ?
    ");
    $stmt->execute([getCurrentUserId(), $limit]);
    return $stmt->fetchAll();
}

function getTypesdemandes() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM types_besoins ORDER BY nom");
    return $stmt->fetchAll();
}

function getStatistiquesValidateur() {
    if (!isLoggedIn()) {
        return [];
    }
    
    $pdo = getDB();
    $userId = getCurrentUserId();

    $user = getCurrentUser();
    $equipeId = $user['equipe_id'] ?? null;

    if ($equipeId) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM demandes d 
            JOIN users u ON d.demandeur_id = u.id 
            WHERE (d.statut = 'Validée' OR d.statut = 'Traitée') 
            AND u.equipe_id = ?
        ");
        $stmt->execute([$equipeId]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) FROM demandes WHERE statut = 'Validée' OR statut = 'Traitée'");
    }
    $validees = $stmt->fetchColumn();

    if ($equipeId) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM demandes d 
            JOIN users u ON d.demandeur_id = u.id 
            WHERE d.statut = 'En attente' 
            AND u.equipe_id = ?
        ");
        $stmt->execute([$equipeId]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) FROM demandes WHERE statut = 'En attente'");
    }
    $en_attente = $stmt->fetchColumn();

    if ($equipeId) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM demandes d 
            JOIN users u ON d.demandeur_id = u.id 
            WHERE d.statut = 'Rejetée' 
            AND u.equipe_id = ?
        ");
        $stmt->execute([$equipeId]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) FROM demandes WHERE statut = 'Rejetée'");
    }
    $rejetees = $stmt->fetchColumn();

    if ($equipeId) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM demandes d 
            JOIN users u ON d.demandeur_id = u.id 
            WHERE u.equipe_id = ?
        ");
        $stmt->execute([$equipeId]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) FROM demandes");
    }
    $total = $stmt->fetchColumn();

    if ($equipeId) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE equipe_id = ?");
        $stmt->execute([$equipeId]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Demandeur'");
    }
    $membres = $stmt->fetchColumn();
    
    return [
        'validees' => $validees,
        'en_attente' => $en_attente,
        'rejetees' => $rejetees,
        'total' => $total,
        'membres' => $membres
    ];
}

function getStatistiques() {
    return getStatistiquesValidateur();
}
?>
