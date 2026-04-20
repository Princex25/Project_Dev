<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../shared/config.php';

final class EnvHelperTest extends TestCase
{
    public function testEnvOrDefaultReturnsDefaultWhenMissing(): void
    {
        putenv('UNIT_TEST_MISSING');
        $this->assertSame('fallback', envOrDefault('UNIT_TEST_MISSING', 'fallback'));
    }

    public function testEnvOrDefaultReturnsValueWhenSet(): void
    {
        putenv('UNIT_TEST_VALUE=hello');
        $this->assertSame('hello', envOrDefault('UNIT_TEST_VALUE', 'fallback'));
    }

    public function testEnvOrDefaultReturnsDefaultWhenEmpty(): void
    {
        putenv('UNIT_TEST_EMPTY=');
        $this->assertSame('fallback', envOrDefault('UNIT_TEST_EMPTY', 'fallback'));
    }

    public function testRoleHelpersReflectSessionRole(): void
    {
        $_SESSION = [];
        $_SESSION['user_role'] = ROLE_ADMIN;

        $this->assertTrue(isAdmin());
        $this->assertFalse(isValidateur());
        $this->assertFalse(isDemandeur());
    }
}
