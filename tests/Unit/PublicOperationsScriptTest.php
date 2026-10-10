<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Puntjes\Generated\Security\PublicOperations;

final class PublicOperationsScriptTest extends TestCase
{
    public function test_it_lists_only_the_operations_with_an_empty_security(): void
    {
        $class = 'PublicOperations'.bin2hex(random_bytes(4));
        $out = sys_get_temp_dir().'/'.$class.'.php';
        exec('php '.escapeshellarg(__DIR__.'/../../scripts/public-operations.php').' '.escapeshellarg(__DIR__.'/../Fixtures/public-operations-spec.json').' '.escapeshellarg($out), $o, $exit);
        $source = (string) file_get_contents($out);

        self::assertSame(0, $exit);
        self::assertStringContainsString("'GET /health',", $source);
        self::assertStringContainsString("'POST /customers/{customer}/wallet',", $source);
        self::assertStringNotContainsString("'GET /customers/{customer}/wallet'", $source);
        self::assertStringNotContainsString('/me', $source);

        file_put_contents($out, str_replace('final class PublicOperations', "final class {$class}", $source));
        require $out;
        $matches = ['Puntjes\\Generated\\Security\\'.$class, 'matches'];

        self::assertTrue($matches('get', '/health'));
        self::assertTrue($matches('POST', '/customers/42/wallet'));
        self::assertFalse($matches('GET', '/customers/42/wallet'));
        self::assertFalse($matches('POST', '/customers//wallet'));
        self::assertFalse($matches('POST', '/customers/42/x/wallet'));
        self::assertFalse($matches('GET', '/health/x'));
        self::assertFalse($matches('GET', '/me'));
    }

    public function test_the_committed_list_holds_the_health_check(): void
    {
        self::assertTrue(PublicOperations::matches('GET', '/health'));
        self::assertFalse(PublicOperations::matches('GET', '/me'));
    }
}
