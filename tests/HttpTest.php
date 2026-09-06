<?php
declare(strict_types=1);

// ITCS333 A4 — HTTP-level tests against `php -S`. DO NOT MODIFY.
use PHPUnit\Framework\TestCase;

final class HttpTest extends TestCase
{
    private static string $base;
    private static $server;

    public static function setUpBeforeClass(): void
    {
        $port = (int) (getenv('A4_PORT') ?: 8091);
        self::$base = "http://127.0.0.1:{$port}";
        $docroot = realpath(__DIR__ . '/../public');
        self::$server = proc_open(
            ['php', '-S', "127.0.0.1:{$port}", '-t', $docroot],
            [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']],
            $pipes
        );
        // wait for server
        for ($i = 0; $i < 50; $i++) {
            $c = @file_get_contents(self::$base . '/index.php');
            if ($c !== false) return;
            usleep(100000);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$server)) {
            proc_terminate(self::$server);
            proc_close(self::$server);
        }
    }

    private function get(string $path): array
    {
        $ctx = stream_context_create(['http' => ['ignore_errors' => true, 'follow_location' => 0]]);
        $body = file_get_contents(self::$base . $path, false, $ctx);
        return [$this->statusCode($http_response_header), $body, $http_response_header];
    }

    private function post(string $path, array $data): array
    {
        $ctx = stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data),
            'ignore_errors' => true,
            'follow_location' => 0,
        ]]);
        $body = file_get_contents(self::$base . $path, false, $ctx);
        return [$this->statusCode($http_response_header), $body, $http_response_header];
    }

    private function statusCode(array $headers): int
    {
        if (isset($headers[0]) && preg_match('#\s(\d{3})\s#', $headers[0], $m)) return (int) $m[1];
        return 0;
    }

    public function test_get_renders_form(): void
    {
        [$code, $body] = $this->get('/index.php');
        $this->assertSame(200, $code);
        $this->assertStringContainsString('<form', $body);
        $this->assertStringContainsString('name="name"', $body);
        $this->assertStringContainsString('name="email"', $body);
        $this->assertStringContainsString('name="message"', $body);
        $this->assertStringNotContainsString('<b>Warning</b>', $body);
        $this->assertStringNotContainsString('<b>Notice</b>', $body);
    }

    public function test_invalid_post_shows_errors_and_repopulates(): void
    {
        [$code, $body] = $this->post('/index.php', ['name' => 'A', 'email' => 'bad', 'message' => 'hi']);
        $this->assertSame(200, $code);
        // inline errors present (some error text near fields)
        $this->assertMatchesRegularExpression('/error|invalid|required|too short/i', $body);
        // repopulated
        $this->assertStringContainsString('value="A"', $body);
        $this->assertStringContainsString('value="bad"', $body);
        $this->assertStringContainsString('hi', $body);
    }

    public function test_valid_post_redirects_prg(): void
    {
        [$code, $body, $headers] = $this->post('/index.php', [
            'name' => 'Ahmed Ali',
            'email' => 'ahmed@uob.edu.bh',
            'message' => 'Hello, this is a valid message.'
        ]);
        $this->assertContains($code, [301, 302, 303, 307, 308], 'expected a redirect status (PRG)');
        $location = '';
        foreach ($headers as $h) if (stripos($h, 'Location:') === 0) $location = trim(substr($h, 9));
        $this->assertNotSame('', $location, 'expected a Location header');
    }

    public function test_output_is_escaped_xss(): void
    {
        $payload = '<script>alert(1)</script>';
        [$code, $body] = $this->post('/index.php', ['name' => $payload, 'email' => 'bad', 'message' => 'hi']);
        $this->assertSame(200, $code);
        $this->assertStringNotContainsString($payload, $body, 'raw XSS payload must not be rendered');
        $this->assertStringContainsString('&lt;script&gt;', $body, 'payload should appear escaped');
    }
}
