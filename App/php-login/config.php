<?php

function envOrDefault($key, $default = null)
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    return $value;
}

$databaseHost = envOrDefault('DB_HOST', '127.0.0.1');
$databaseName = envOrDefault('DB_NAME', 'gestion_demandes');
$databaseUser = envOrDefault('DB_USER', 'root');
$databasePassword = envOrDefault('DB_PASS', 'root');
$portsToTry = [];

$envPort = envOrDefault('DB_PORT');
if ($envPort !== null && ctype_digit((string) $envPort)) {
    $portsToTry[] = (int) $envPort;
}

$legacyPort = envOrDefault('LOGIN_APP_DB_PORT');
if ($legacyPort !== null && ctype_digit((string) $legacyPort)) {
    $portsToTry[] = (int) $legacyPort;
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
