<?php

$databaseHost = '127.0.0.1';
$databaseName = 'login_app';
$databaseUser = 'root';
$databasePassword = 'root';
$portsToTry = [];

$envPort = getenv('LOGIN_APP_DB_PORT');
if ($envPort !== false && ctype_digit($envPort)) {
    $portsToTry[] = (int) $envPort;
}

$portsToTry = array_unique(array_merge($portsToTry, [3306, 8889]));

$lastException = null;
$pdo = null;

foreach ($portsToTry as $databasePort) {
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $databaseHost, $databasePort, $databaseName);
    try {
        $pdo = new PDO($dsn, $databaseUser, $databasePassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        break;
    } catch (PDOException $exception) {
        $lastException = $exception;
    }
}

if (!$pdo instanceof PDO) {
    http_response_code(500);
    exit('Database connection failed: ' . ($lastException ? $lastException->getMessage() : 'Unable to connect to any configured port.'));
}