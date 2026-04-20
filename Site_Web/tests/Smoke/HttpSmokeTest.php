<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class HttpSmokeTest extends TestCase
{
    public function testLoginPageResponds(): void
    {
        $baseUrl = getenv('APP_BASE_URL') ?: 'http://localhost:8080';
        $url = rtrim($baseUrl, '/') . '/admin2/php-login/index.php';

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 10,
                'ignore_errors' => true,
            ],
        ]);

        $body = @file_get_contents($url, false, $context);
        if ($body === false && empty($http_response_header)) {
            $this->markTestSkipped('App not reachable. Start containers and set APP_BASE_URL if needed.');
        }

        $statusLine = $http_response_header[0] ?? '';
        if (!preg_match('/\s(\d{3})\s/', $statusLine, $matches)) {
            $this->fail('Unable to determine HTTP status code from response.');
        }

        $statusCode = (int) $matches[1];
        $this->assertSame(200, $statusCode);
        $this->assertStringContainsString('Connexion', $body ?: '');
    }
}
