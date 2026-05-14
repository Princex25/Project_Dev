<?php

require_once __DIR__ . '/../config.php';
requireLogin();

$pdo = getConnection();
$user = getCurrentUser();
$stats = getStatistiques();

$error = '';

function getDepartements($pdo) {
    return $pdo->query("SELECT DISTINCT id, nom FROM departements ORDER BY nom")->fetchAll();
}

function getEquipes($pdo) {
    return $pdo->query("SELECT DISTINCT id, nom FROM equipes ORDER BY nom")->fetchAll();
}

function traiterMiseAJour($pdo, $userId) {
    global $error;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }
    
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $departement_id = intval($_POST['departement_id']);
    $equipe_id = intval($_POST['equipe_id']);

    if (empty($email)) {
        $error = "L'email est obligatoire.";
        return false;
    }

    $stmt = $pdo->prepare("
        UPDATE users 
        SET email = ?, telephone = ?, departement_id = ?, equipe_id = ?
        WHERE id = ?
    ");
    
    $result = $stmt->execute([
        $email,
        $telephone,
        $departement_id ?: null,
        $equipe_id ?: null,
        $userId
    ]);
    
    if ($result) {
        header('Location: profile.php?success=1');
        exit();
    } else {
        $error = "Erreur lors de la mise à jour du profil.";
        return false;
    }
}

$departements = getDepartements($pdo);
$equipes = getEquipes($pdo);

traiterMiseAJour($pdo, $_SESSION['user_id']);

$user = getCurrentUser();
$stats = getStatistiques();
?>
