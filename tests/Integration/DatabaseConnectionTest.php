<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DatabaseConnectionTest extends TestCase
{
    private function getPdoOrSkip(): PDO
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $dbName = getenv('DB_NAME') ?: 'gestion_demandes';
        $user = getenv('DB_USER') ?: 'app';
        $pass = getenv('DB_PASS') ?: 'app';

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName);

        try {
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 3,
            ]);
        } catch (PDOException $e) {
            $this->markTestSkipped('Database not reachable. Ensure containers are running and env vars are set. Error: ' . $e->getMessage());
        }
    }

    public function testUsersTableHasSeedData(): void
    {
        $pdo = $this->getPdoOrSkip();
        $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $this->assertGreaterThan(0, $count);
    }

    public function testAdminUserExists(): void
    {
        $pdo = $this->getPdoOrSkip();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $stmt->execute(['admin@example.com']);
        $count = (int) $stmt->fetchColumn();
        $this->assertSame(1, $count);
    }
}