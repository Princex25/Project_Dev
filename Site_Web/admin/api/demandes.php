<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $db = getDB();
    
    switch ($method) {
        case 'GET':

            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'assignment':

                        $stmt = $db->query("
                            SELECT 
                                d.id,
                                d.description,
                                d.statut,
                                d.priorite,
                                d.service_id,
                                d.date_creation,
                                u.nom_complet as demandeur_nom,
                                t.nom as type_nom,
                                s.nom as service_nom
                            FROM demandes d
                            JOIN users u ON d.demandeur_id = u.id
                            JOIN types_besoins t ON d.type_id = t.id
                            LEFT JOIN services s ON d.service_id = s.id
                            WHERE d.statut IN ('En attente', 'En cours')
                            ORDER BY d.date_creation ASC
                        ");
                        $demandes = $stmt->fetchAll();

                        $stmt = $db->query("SELECT COUNT(*) as count FROM demandes WHERE statut IN ('En attente', 'En cours')");
                        $pending = $stmt->fetch()['count'];
                        
                        $stmt = $db->query("SELECT COUNT(*) as count FROM demandes WHERE service_id IS NULL AND statut IN ('En attente', 'En cours')");
                        $unassigned = $stmt->fetch()['count'];
                        
                        $stmt = $db->query("SELECT COUNT(*) as count FROM demandes WHERE DATE(date_modification) = CURDATE()");
                        $modified = $stmt->fetch()['count'];
                        
                        echo json_encode([
                            'success' => true,
                            'demandes' => $demandes,
                            'stats' => [
                                'pending' => $pending,
                                'unassigned' => $unassigned,
                                'modified_today' => $modified
                            ]
                        ]);
                        break;
                        
                    case 'prioritization':

                        $stmt = $db->query("
                            SELECT 
                                d.id,
                                d.description,
                                d.statut,
                                d.priorite,
                                d.service_id,
                                d.date_creation,
                                u.nom_complet as demandeur_nom,
                                t.nom as type_nom,
                                s.nom as service_nom
                            FROM demandes d
                            JOIN users u ON d.demandeur_id = u.id
                            JOIN types_besoins t ON d.type_id = t.id
                            LEFT JOIN services s ON d.service_id = s.id
                            WHERE d.statut IN ('En attente', 'En cours')
                            ORDER BY 
                                CASE d.priorite 
                                    WHEN 'Urgente' THEN 1 
                                    WHEN 'Haute' THEN 2 
                                    WHEN 'Moyenne' THEN 3 
                                    ELSE 4 
                                END,
                                d.date_creation ASC
                        ");
                        $demandes = $stmt->fetchAll();

                        $stmt = $db->query("SELECT COUNT(*) as count FROM demandes WHERE statut IN ('En attente', 'En cours')");
                        $pending = $stmt->fetch()['count'];
                        
                        $stmt = $db->query("SELECT COUNT(*) as count FROM demandes WHERE service_id IS NULL AND statut IN ('En attente', 'En cours')");
                        $unassigned = $stmt->fetch()['count'];
                        
                        $stmt = $db->query("SELECT COUNT(*) as count FROM demandes WHERE DATE(date_modification) = CURDATE()");
                        $modified = $stmt->fetch()['count'];
                        
                        echo json_encode([
                            'success' => true,
                            'demandes' => $demandes,
                            'stats' => [
                                'pending' => $pending,
                                'unassigned' => $unassigned,
                                'modified_today' => $modified
                            ]
                        ]);
                        break;
                }
            } elseif (isset($_GET['id'])) {

                $stmt = $db->prepare("
                    SELECT 
                        d.*,
                        u.nom_complet as demandeur_nom,
                        u.email as demandeur_email,
                        t.nom as type_nom,
                        s.nom as service_nom
                    FROM demandes d
                    JOIN users u ON d.demandeur_id = u.id
                    JOIN types_besoins t ON d.type_id = t.id
                    LEFT JOIN services s ON d.service_id = s.id
                    WHERE d.id = ?
                ");
                $stmt->execute([$_GET['id']]);
                $demande = $stmt->fetch();
                
                if ($demande) {
                    echo json_encode(['success' => true, 'demande' => $demande]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Demande non trouvée']);
                }
            } else {

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
                ");
                $demandes = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'demandes' => $demandes]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['action']) && $data['action'] === 'bulk_update') {

                $updates = $data['updates'] ?? [];
                $count = 0;
                
                foreach ($updates as $update) {
                    $stmt = $db->prepare("UPDATE demandes SET service_id = ?, date_modification = NOW() WHERE id = ?");
                    $stmt->execute([$update['service_id'], $update['id']]);
                    $count++;
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => "$count demande(s) mise(s) à jour"
                ]);
            } else {

                if (empty($data['demandeur_id']) || empty($data['type_id']) || empty($data['description'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                    break;
                }
                
                $stmt = $db->prepare("
                    INSERT INTO demandes (demandeur_id, type_id, description, priorite) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $data['demandeur_id'],
                    $data['type_id'],
                    $data['description'],
                    $data['priorite'] ?? 'Normale'
                ]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Demande créée avec succès',
                    'id' => $db->lastInsertId()
                ]);
            }
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? $_GET['id'] ?? null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID requis']);
                break;
            }
            
            $updates = [];
            $params = [];
            
            if (isset($data['statut'])) {
                $updates[] = "statut = ?";
                $params[] = $data['statut'];
            }
            
            if (isset($data['service_id'])) {
                $updates[] = "service_id = ?";
                $params[] = $data['service_id'] ?: null;
            }
            
            if (isset($data['priorite'])) {
                $updates[] = "priorite = ?";
                $params[] = $data['priorite'];
            }
            
            if (isset($data['raison_rejet'])) {
                $updates[] = "raison_rejet = ?";
                $params[] = $data['raison_rejet'];
            }
            
            if (!empty($updates)) {
                $updates[] = "date_modification = NOW()";
                $params[] = $id;
                $stmt = $db->prepare("UPDATE demandes SET " . implode(", ", $updates) . " WHERE id = ?");
                $stmt->execute($params);
            }
            
            echo json_encode(['success' => true, 'message' => 'Demande mise à jour avec succès']);
            break;
            
        case 'DELETE':
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID requis']);
                break;
            }
            
            $stmt = $db->prepare("DELETE FROM demandes WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Demande supprimée avec succès']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>
