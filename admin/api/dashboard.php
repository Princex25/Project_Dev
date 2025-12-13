<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/database.php';

try {
    $db = getDB();

    $stmt = $db->query("SELECT COUNT(*) as total FROM demandes");
    $total = $stmt->fetch()['total'];

    $stmt = $db->query("SELECT COUNT(*) as count FROM demandes WHERE service_id IS NULL");
    $toAssign = $stmt->fetch()['count'];

    $stmt = $db->query("SELECT COUNT(*) as count FROM demandes WHERE priorite IN ('Urgente', 'Haute') AND statut IN ('En attente', 'En cours')");
    $late = $stmt->fetch()['count'];

    $stmt = $db->query("
        SELECT t.nom, COUNT(d.id) as count 
        FROM types_besoins t 
        LEFT JOIN demandes d ON t.id = d.type_id 
        GROUP BY t.id, t.nom 
        ORDER BY count DESC
    ");
    $typeDistribution = $stmt->fetchAll();

    $typeTotal = array_sum(array_column($typeDistribution, 'count'));
    foreach ($typeDistribution as &$type) {
        $type['percentage'] = $typeTotal > 0 ? round(($type['count'] / $typeTotal) * 100) : 0;
    }

    $stmt = $db->query("
        SELECT statut, COUNT(*) as count 
        FROM demandes 
        GROUP BY statut
    ");
    $statusDistribution = $stmt->fetchAll();

    foreach ($statusDistribution as &$status) {
        $status['percentage'] = $total > 0 ? round(($status['count'] / $total) * 100) : 0;
    }

    $stmt = $db->query("
        SELECT 
            d.id,
            d.description,
            d.statut,
            d.priorite,
            d.date_creation,
            u.nom_complet as demandeur_nom,
            t.nom as type_nom,
            s.nom as service_nom
        FROM demandes d
        JOIN users u ON d.demandeur_id = u.id
        JOIN types_besoins t ON d.type_id = t.id
        LEFT JOIN services s ON d.service_id = s.id
        ORDER BY d.date_creation DESC
        LIMIT 50
    ");
    $demandes = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total' => $total,
            'to_assign' => $toAssign,
            'late' => $late
        ],
        'type_distribution' => $typeDistribution,
        'status_distribution' => $statusDistribution,
        'demandes' => $demandes
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>
