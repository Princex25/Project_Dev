<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function envOrDefault($key, $default = null)
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    return $value;
}

$dbPortValue = envOrDefault('DB_PORT', '3306');
$dbPort = (ctype_digit((string) $dbPortValue)) ? (int) $dbPortValue : 3306;

$baseUrlRaw = envOrDefault('BASE_URL', '/admin2');
$baseUrl = rtrim($baseUrlRaw, '/');
if ($baseUrlRaw === '/' || $baseUrlRaw === '') {
    $baseUrl = '';
}

define('DB_HOST', envOrDefault('DB_HOST', 'localhost'));
define('DB_NAME', envOrDefault('DB_NAME', 'gestion_demandes'));
define('DB_USER', envOrDefault('DB_USER', 'root'));
define('DB_PASS', envOrDefault('DB_PASS', 'root'));
define('DB_PORT', $dbPort);

define('BASE_URL', $baseUrl);
define('ADMIN_URL', BASE_URL . '/admin');
define('DEMANDEUR_URL', BASE_URL . '/Demander');
define('VALIDATEUR_URL', BASE_URL . '/Validateur');
define('LOGIN_URL', BASE_URL . '/php-login');

define('ROLE_ADMIN', 'Administrateur');
define('ROLE_VALIDATEUR', 'Validateur');
define('ROLE_DEMANDEUR', 'Demandeur');

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                DB_HOST,
                DB_PORT,
                DB_NAME
            );

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {

            die("Erreur de connexion à la base de données: " . $e->getMessage() .
                "<br><br><strong>Assurez-vous que:</strong><br>" .
                "1. MySQL est démarré<br>" .
                "2. La base de données 'gestion_demandes' existe<br>" .
                "3. Importez le fichier SQL: /admin2/database/unified_database.sql");
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function __clone() {}
}

function getDB()
{
    return Database::getInstance()->getConnection();
}

function getConnection()
{
    return getDB();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUserId()
{
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole()
{
    return $_SESSION['user_role'] ?? null;
}

function getCurrentUser()
{
    if (!isLoggedIn()) {
        return null;
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT u.*, d.nom as departement_nom, e.nom as equipe_nom 
        FROM users u 
        LEFT JOIN departements d ON u.departement_id = d.id 
        LEFT JOIN equipes e ON u.equipe_id = e.id 
        WHERE u.id = ?
    ");
    $stmt->execute([getCurrentUserId()]);
    return $stmt->fetch();
}

function hasRole($role)
{
    return getCurrentUserRole() === $role;
}

function isAdmin()
{
    return hasRole(ROLE_ADMIN);
}

function isValidateur()
{
    return hasRole(ROLE_VALIDATEUR);
}

function isDemandeur()
{
    return hasRole(ROLE_DEMANDEUR);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ' . LOGIN_URL . '/index.php');
        exit();
    }
}

function redirectToUserSpace()
{
    $role = getCurrentUserRole();

    switch ($role) {
        case ROLE_ADMIN:
            header('Location: ' . ADMIN_URL . '/index.php');
            break;
        case ROLE_VALIDATEUR:
            header('Location: ' . VALIDATEUR_URL . '/index.php');
            break;
        case ROLE_DEMANDEUR:
            header('Location: ' . DEMANDEUR_URL . '/index.php');
            break;
        default:
            header('Location: ' . LOGIN_URL . '/index.php');
    }
    exit();
}

function requireRole($allowedRoles)
{
    requireLogin();

    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }

    if (!in_array(getCurrentUserRole(), $allowedRoles)) {

        redirectToUserSpace();
    }
}

function logout()
{

    if (isset($_COOKIE['remember_token'])) {
        $pdo = getDB();
        $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
        $stmt->execute([getCurrentUserId()]);

        setcookie('remember_token', '', time() - 3600, '/');
    }

    session_destroy();

    header('Location: ' . LOGIN_URL . '/index.php');
    exit();
}

function createNotification($userId, $message, $type = 'info', $demandeId = null)
{
    $pdo = getDB();
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, demande_id, message, type) 
        VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$userId, $demandeId, $message, $type]);
}

function getUnreadNotifications($userId, $limit = 10)
{
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT * FROM notifications 
        WHERE user_id = ? AND lu = 0 
        ORDER BY date_creation DESC 
        LIMIT ?
    ");
    $stmt->execute([$userId, $limit]);
    return $stmt->fetchAll();
}

function countUnreadNotifications($userId)
{
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND lu = 0");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    return $result['count'];
}
