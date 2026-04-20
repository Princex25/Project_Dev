<?php

require_once __DIR__ . '/../config.php';
requireLogin();

$pdo = getConnection();
$user = getCurrentUser();
$stats = getStatistiques();

$departement = $user['departement_nom'] ?? 'Non assigné';
$equipe = $user['equipe_nom'] ?? 'Non assignée';
?>
