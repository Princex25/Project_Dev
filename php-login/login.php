<?php

require_once __DIR__ . '/../shared/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    $_SESSION['login_error'] = 'Adresse e-mail ou mot de passe invalide.';
    header('Location: index.php');
    exit();
}

try {
    $pdo = getDB();

    $sql = 'SELECT id, nom_complet, email, mot_de_passe, role, statut FROM users WHERE email = :email LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->execute([':email' => $email]);
    $user = $statement->fetch();

    $passwordValid = false;
    if ($user) {

        if (strpos($user['mot_de_passe'], '$2y$') === 0) {
            $passwordValid = password_verify($password, $user['mot_de_passe']);
        } else {

            $passwordValid = ($password === $user['mot_de_passe']);
        }
    }
    
    if ($user && $passwordValid) {

        if ($user['statut'] !== 'Actif') {
            $_SESSION['login_error'] = 'Votre compte est désactivé. Contactez l\'administrateur.';
            header('Location: index.php');
            exit();
        }

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['nom_complet'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        $updateStmt = $pdo->prepare('UPDATE users SET derniere_connexion = NOW() WHERE id = :id');
        $updateStmt->execute([':id' => $user['id']]);

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, [
                'expires' => time() + 60 * 60 * 24 * 30, // 30 jours
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            
            $rememberStmt = $pdo->prepare('UPDATE users SET remember_token = :token WHERE id = :id');
            $rememberStmt->execute([
                ':token' => password_hash($token, PASSWORD_DEFAULT),
                ':id' => $user['id'],
            ]);
        }

        redirectToUserSpace();
        
    } else {
        $_SESSION['login_error'] = 'Identifiants incorrects. Veuillez réessayer.';
        header('Location: index.php');
        exit();
    }
    
} catch (PDOException $e) {
    $_SESSION['login_error'] = 'Erreur de connexion à la base de données.';
    header('Location: index.php');
    exit();
}
