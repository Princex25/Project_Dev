<?php
require_once __DIR__ . '/../../shared/config.php';

requireRole([ROLE_DEMANDEUR, ROLE_ADMIN]);

$pdo = getDB();

function getStatistiques($pdo, $userId)
{
    if (!$pdo) return ['total' => 0, 'validees' => 0, 'en_attente' => 0];

    try {
        $stats = [];

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM demandes WHERE demandeur_id = ?");
        $stmt->execute([$userId]);
        $stats['total'] = $stmt->fetch()['total'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM demandes WHERE demandeur_id = ? AND statut = 'Validée'");
        $stmt->execute([$userId]);
        $stats['validees'] = $stmt->fetch()['count'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM demandes WHERE demandeur_id = ? AND statut = 'En attente'");
        $stmt->execute([$userId]);
        $stats['en_attente'] = $stmt->fetch()['count'];

        return $stats;
    } catch (PDOException $e) {
        return ['total' => 0, 'validees' => 0, 'en_attente' => 0];
    }
}

function getStatistiquesParUrgence($pdo, $userId)
{
    if (!$pdo) return ['faible' => 0, 'moyenne' => 0, 'urgente' => 0];

    try {
        $stats = [];

        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM demandes WHERE demandeur_id = ? AND priorite IN ('Faible', 'Normale')");
        $stmt->execute([$userId]);
        $stats['faible'] = $stmt->fetch()['count'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM demandes WHERE demandeur_id = ? AND priorite = 'Moyenne'");
        $stmt->execute([$userId]);
        $stats['moyenne'] = $stmt->fetch()['count'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM demandes WHERE demandeur_id = ? AND priorite IN ('Haute', 'Urgente')");
        $stmt->execute([$userId]);
        $stats['urgente'] = $stmt->fetch()['count'];

        return $stats;
    } catch (PDOException $e) {
        return ['faible' => 0, 'moyenne' => 0, 'urgente' => 0];
    }
}

function getNotificationsDemandeur($userId, $limit = 10)
{
    $pdo = getDB();
    try {
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY date_creation DESC LIMIT ?");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getStatutColor($statut)
{
    switch ($statut) {
        case 'Validée':
            return 'bg-cyan';
        case 'En cours de validation':
            return 'bg-orange';
        case 'En cours':
            return 'bg-orange';
        case 'Rejetée':
            return 'bg-red';
        case 'En attente':
            return 'bg-orange';
        case 'Traitée':
            return 'bg-green';
        default:
            return 'bg-gray';
    }
}

function getStatutLabel($statut)
{
    switch ($statut) {
        case 'Validée':
            return 'Validée';
        case 'En cours de validation':
            return 'En cours';
        case 'Rejetée':
            return 'Rejetée';
        default:
            return $statut;
    }
}

function getUrgenceColor($urgence)
{
    switch ($urgence) {
        case 'Urgente':
        case 'Haute':
            return 'text-red';
        case 'Moyenne':
            return 'text-yellow';
        case 'Faible':
        case 'Normale':
            return 'text-green';
        default:
            return 'text-gray';
    }
}
