<?php

declare(strict_types=1);

namespace Puntjes\Tests\Contract\Support;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Shared setup for the contract suite.
 *
 * The suites create customers, transactions and redemptions, so they refuse any base URL
 * that is not a local app. A refusal is a failure, never a skip: a skipped suite is green
 * with zero calls made.
 */
final class Live
{
    public const LOCAL_HOSTS = ['localhost', '127.0.0.1', 'laravel.test', 'host.docker.internal'];

    /** @var array<string, array<string, true>> */
    private static array $hits = [];

    public static function baseUrl(): string
    {
        $baseUrl = rtrim(self::env('PUNTJES_BASE_URL'), '/');
        self::assertLocal($baseUrl);

        return $baseUrl;
    }

    public static function assertLocal(string $baseUrl): void
    {
        $host = parse_url($baseUrl, PHP_URL_HOST);

        if (! is_string($host) || ! in_array(strtolower($host), self::LOCAL_HOSTS, true)) {
            throw new RuntimeException("Refusing to run the contract suite against [{$baseUrl}]: it creates customers and points, so it only runs against a local app.");
        }
    }

    /** @return array{code: string, customer: int} */
    public static function voucher(): array
    {
        $voucher = json_decode(self::env('SPIKE_VOUCHER'), true);

        if (! is_array($voucher) || ! is_string($voucher['code'] ?? null) || ! is_int($voucher['customer'] ?? null)) {
            throw new RuntimeException('SPIKE_VOUCHER must be the JSON line tests/Contract/fixtures/voucher.php prints.');
        }

        return $voucher;
    }

    public static function hit(string $client, string $operationId): void
    {
        self::$hits[$client][$operationId] = true;
    }

    /** @return list<string> */
    public static function missed(string $client): array
    {
        $spec = json_decode((string) file_get_contents(__DIR__.'/../../../spec/api.json'), true);
        $all = [];

        foreach ($spec['paths'] as $operations) {
            foreach ($operations as $verb => $operation) {
                if (in_array($verb, ['get', 'post', 'put', 'patch', 'delete'], true)) {
                    $all[] = $operation['operationId'];
                }
            }
        }

        TestCase::assertCount(34, $all);

        return array_values(array_diff($all, array_keys(self::$hits[$client] ?? [])));
    }

    public static function runId(): string
    {
        static $id = null;

        return $id ??= 'sdk-'.bin2hex(random_bytes(4));
    }

    private static function env(string $name): string
    {
        $value = getenv($name);

        if (! is_string($value) || $value === '') {
            throw new RuntimeException("Set {$name} to run the contract suite.");
        }

        return $value;
    }
}
