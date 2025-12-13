<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'root';
$pass = 'root';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $sqlFile = __DIR__ . '/database/demander.sql';

        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);

            $pdo->exec($sql);

            $success = true;
            $message = 'Base de données installée avec succès! Vous pouvez maintenant accéder à l\'application.';
        } else {
            $message = 'Fichier SQL introuvable: ' . $sqlFile;
        }
    } catch (PDOException $e) {
        $message = 'Erreur: ' . $e->getMessage();

        if (strpos($e->getMessage(), 'Access denied') !== false) {
            $message .= '<br><br><strong>Suggestion:</strong> Essayez de modifier le mot de passe dans ce fichier (ligne 12). Pour MAMP, essayez un mot de passe vide: <code>$pass = \'\';</code>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Demander</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #1a1f2e 0%, #0f1419 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .container {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 40px;
        max-width: 500px;
        width: 100%;
        text-align: center;
    }

    h1 {
        color: #fff;
        margin-bottom: 10px;
        font-size: 2rem;
    }

    .subtitle {
        color: #9ca3af;
        margin-bottom: 30px;
    }

    .message {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .message.success {
        background: rgba(34, 197, 94, 0.2);
        border: 1px solid #22c55e;
        color: #22c55e;
    }

    .message.error {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid #ef4444;
        color: #ef4444;
    }

    .btn {
        display: inline-block;
        padding: 15px 40px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.3s;
    }

    .btn:hover {
        background: #1d4ed8;
    }

    .btn-success {
        background: #22c55e;
    }

    .btn-success:hover {
        background: #16a34a;
    }

    .info {
        margin-top: 30px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        text-align: left;
    }

    .info h3 {
        color: #fff;
        margin-bottom: 10px;
    }

    .info p,
    .info li {
        color: #9ca3af;
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .info code {
        background: rgba(0, 0, 0, 0.3);
        padding: 2px 6px;
        border-radius: 4px;
        color: #60a5fa;
    }

    ul {
        margin-left: 20px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>🚀 Installation</h1>
        <p class="subtitle">Système de gestion des demandes</p>

        <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <a href="index.php" class="btn btn-success">Accéder à l'application →</a>
        <?php else: ?>
        <form method="POST">
            <button type="submit" class="btn">Installer la base de données</button>
        </form>

        <div class="info">
            <h3>Ce script va:</h3>
            <ul>
                <li>Créer la base de données <code>demander_db</code></li>
                <li>Créer toutes les tables nécessaires</li>
                <li>Insérer les données par défaut (utilisateurs, demandes, notifications)</li>
            </ul>
            <p style="margin-top: 15px;">
                <strong>Utilisateur par défaut:</strong><br>
                Email: <code>ahmed@example.com</code><br>
                Mot de passe: <code>password123</code>
            </p>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>