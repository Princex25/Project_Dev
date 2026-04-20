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

function isTeamManagedByValidateur(PDO $db, $teamId) {
    $stmt = $db->prepare("SELECT 1 FROM equipes e JOIN users v ON v.equipe_id = e.id AND v.role = 'Validateur' WHERE e.id = ? LIMIT 1");
    $stmt->execute([$teamId]);
    return (bool) $stmt->fetchColumn();
}

function getManagedTeams(PDO $db) {
    $sql = "
        SELECT e.id, e.nom,
               GROUP_CONCAT(v.nom_complet ORDER BY v.nom_complet SEPARATOR ', ') AS validateurs
        FROM equipes e
        JOIN users v ON v.equipe_id = e.id AND v.role = 'Validateur'
        GROUP BY e.id, e.nom
        ORDER BY e.nom
    ";
    return $db->query($sql)->fetchAll();
}

try {
    $db = getDB();
    
    switch ($method) {
        case 'GET':
            if (isset($_GET['action']) && $_GET['action'] === 'teams') {
                $teams = getManagedTeams($db);
                echo json_encode(['success' => true, 'teams' => $teams]);
                break;
            }
            if (isset($_GET['action']) && $_GET['action'] === 'current') {

                $stmt = $db->query("SELECT id, nom_complet, email, role, statut, avatar, date_creation, equipe_id FROM users WHERE id = 1");
                $user = $stmt->fetch();
                
                echo json_encode(['success' => true, 'user' => $user]);
            } elseif (isset($_GET['id'])) {

                $stmt = $db->prepare("SELECT id, nom_complet, email, role, statut, avatar, date_creation, equipe_id FROM users WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $user = $stmt->fetch();
                
                if ($user) {
                    echo json_encode(['success' => true, 'user' => $user]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
                }
            } else {

                $stmt = $db->query("SELECT id, nom_complet, email, role, statut, avatar, date_creation, equipe_id FROM users ORDER BY id");
                $users = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'users' => $users]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['nom_complet']) || empty($data['email']) || empty($data['mot_de_passe'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                break;
            }

            $role = $data['role'] ?? 'Demandeur';
            $equipeId = isset($data['equipe_id']) ? (int)$data['equipe_id'] : null;
            $equipeNom = isset($data['equipe_nom']) ? trim($data['equipe_nom']) : '';
            if ($role === 'Demandeur') {
                if (!$equipeId) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Veuillez sélectionner une équipe pour ce demandeur']);
                    break;
                }
                if (!isTeamManagedByValidateur($db, $equipeId)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Équipe invalide ou sans validateur']);
                    break;
                }
            } elseif ($role === 'Validateur') {
                if ($equipeNom === '') {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Veuillez saisir le nom de l\'équipe à créer pour ce validateur']);
                    break;
                }
                $equipeId = null;
            } else {
                $equipeId = $equipeId ?: null;
            }

            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
                break;
            }

            $hashedPassword = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
            
            $db->beginTransaction();

            $stmt = $db->prepare("
                INSERT INTO users (nom_complet, email, mot_de_passe, role, statut, equipe_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['nom_complet'],
                $data['email'],
                $hashedPassword,
                $role,
                $data['statut'] ?? 'Actif',
                $role === 'Validateur' ? null : $equipeId
            ]);
            
            $newId = (int) $db->lastInsertId();

            $createdTeamId = null;
            if ($role === 'Validateur') {
                $stmt = $db->prepare("INSERT INTO equipes (nom, departement_id, chef_equipe_id) VALUES (?, NULL, ?)");
                $stmt->execute([$equipeNom, $newId]);
                $createdTeamId = (int) $db->lastInsertId();

                $stmt = $db->prepare("UPDATE users SET equipe_id = ? WHERE id = ?");
                $stmt->execute([$createdTeamId, $newId]);
            }

            $db->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Utilisateur créé avec succès',
                'id' => $newId,
                'equipe_id' => $createdTeamId ?: $equipeId,
                'equipe_nom' => $createdTeamId ? $equipeNom : null
            ]);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['id']) && empty($_GET['id'])) {

                $userId = 1;
                
                $updates = [];
                $params = [];
                
                if (!empty($data['nom_complet'])) {
                    $updates[] = "nom_complet = ?";
                    $params[] = $data['nom_complet'];
                }
                
                if (!empty($data['email'])) {

                    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                    $stmt->execute([$data['email'], $userId]);
                    if ($stmt->fetch()) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
                        break;
                    }
                    $updates[] = "email = ?";
                    $params[] = $data['email'];
                }
                
                if (!empty($data['new_password'])) {

                    if (!empty($data['current_password'])) {
                        $stmt = $db->prepare("SELECT mot_de_passe FROM users WHERE id = ?");
                        $stmt->execute([$userId]);
                        $user = $stmt->fetch();
                        
                        if (!password_verify($data['current_password'], $user['mot_de_passe'])) {
                            http_response_code(400);
                            echo json_encode(['success' => false, 'message' => 'Mot de passe actuel incorrect']);
                            break;
                        }
                    }
                    
                    $updates[] = "mot_de_passe = ?";
                    $params[] = password_hash($data['new_password'], PASSWORD_DEFAULT);
                }
                
                if (!empty($updates)) {
                    $params[] = $userId;
                    $stmt = $db->prepare("UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?");
                    $stmt->execute($params);
                }
                
                echo json_encode(['success' => true, 'message' => 'Profil mis à jour avec succès']);
            } else {

                $userId = $data['id'] ?? $_GET['id'];
                $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $currentUser = $stmt->fetch();
                if (!$currentUser) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
                    break;
                }
                $roleToApply = $data['role'] ?? $currentUser['role'];
                
                $updates = [];
                $params = [];
                
                if (!empty($data['nom_complet'])) {
                    $updates[] = "nom_complet = ?";
                    $params[] = $data['nom_complet'];
                }
                
                if (!empty($data['email'])) {
                    $updates[] = "email = ?";
                    $params[] = $data['email'];
                }
                
                if (!empty($data['role'])) {
                    $updates[] = "role = ?";
                    $params[] = $data['role'];
                }

                if (array_key_exists('equipe_id', $data)) {
                    $equipeId = $data['equipe_id'] !== '' ? (int)$data['equipe_id'] : null;
                    if ($roleToApply === 'Demandeur') {
                        if (!$equipeId) {
                            http_response_code(400);
                            echo json_encode(['success' => false, 'message' => 'Veuillez sélectionner une équipe pour ce demandeur']);
                            break;
                        }
                        if (!isTeamManagedByValidateur($db, $equipeId)) {
                            http_response_code(400);
                            echo json_encode(['success' => false, 'message' => 'Équipe invalide ou sans validateur']);
                            break;
                        }
                    }
                    $updates[] = "equipe_id = ?";
                    $params[] = ($roleToApply === 'Demandeur') ? $equipeId : null;
                }
                
                if (isset($data['statut'])) {
                    $updates[] = "statut = ?";
                    $params[] = $data['statut'];
                }
                
                if (!empty($updates)) {
                    $params[] = $userId;
                    $stmt = $db->prepare("UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?");
                    $stmt->execute($params);
                }
                
                echo json_encode(['success' => true, 'message' => 'Utilisateur mis à jour avec succès']);
            }
            break;
            
        case 'DELETE':
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID requis']);
                break;
            }
            
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé avec succès']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
            }
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
