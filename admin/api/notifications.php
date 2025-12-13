<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();
    
    switch ($method) {
        case 'GET':

            $userId = 1;
            
            $stmt = $db->prepare("
                SELECT id, message, type, lu, date_creation 
                FROM notifications 
                WHERE user_id = ? 
                ORDER BY date_creation DESC 
                LIMIT 20
            ");
            $stmt->execute([$userId]);
            $notifications = $stmt->fetchAll();

            foreach ($notifications as &$notif) {
                $notif['time_ago'] = timeAgo($notif['date_creation']);
            }

            $stmt = $db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND lu = 0");
            $stmt->execute([$userId]);
            $unreadCount = $stmt->fetch()['count'];
            
            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['action']) && $data['action'] === 'mark_all_read') {
                $userId = 1;
                $stmt = $db->prepare("UPDATE notifications SET lu = 1 WHERE user_id = ?");
                $stmt->execute([$userId]);
                
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Action invalide']);
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

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'À l\'instant';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' min';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . 'h';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . 'j';
    } else {
        return date('d/m/Y', $time);
    }
}
?>
