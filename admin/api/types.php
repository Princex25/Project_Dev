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
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM types_besoins WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $type = $stmt->fetch();
                
                if ($type) {
                    echo json_encode(['success' => true, 'type' => $type]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Type non trouvé']);
                }
            } else {
                $stmt = $db->query("SELECT * FROM types_besoins ORDER BY id");
                $types = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'types' => $types]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['nom'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Le nom est requis']);
                break;
            }
            
            $stmt = $db->prepare("INSERT INTO types_besoins (nom, description) VALUES (?, ?)");
            $stmt->execute([
                $data['nom'],
                $data['description'] ?? ''
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Type créé avec succès',
                'id' => $db->lastInsertId()
            ]);
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
            
            if (!empty($data['nom'])) {
                $updates[] = "nom = ?";
                $params[] = $data['nom'];
            }
            
            if (isset($data['description'])) {
                $updates[] = "description = ?";
                $params[] = $data['description'];
            }
            
            if (!empty($updates)) {
                $params[] = $id;
                $stmt = $db->prepare("UPDATE types_besoins SET " . implode(", ", $updates) . " WHERE id = ?");
                $stmt->execute($params);
            }
            
            echo json_encode(['success' => true, 'message' => 'Type mis à jour avec succès']);
            break;
            
        case 'DELETE':
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID requis']);
                break;
            }

            $stmt = $db->prepare("SELECT COUNT(*) as count FROM demandes WHERE type_id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetch()['count'];
            
            if ($count > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ce type est utilisé par des demandes et ne peut pas être supprimé']);
                break;
            }
            
            $stmt = $db->prepare("DELETE FROM types_besoins WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Type supprimé avec succès']);
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
