<?php

declare(strict_types=1);

namespace Puntjes\Tests\Contract\Support;

use PHPUnit\Framework\Assert;
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

    /** The access token a partner got for the admin who allowed its connection. */
    public static function partnerToken(): string
    {
        return self::env('PUNTJES_PARTNER_TOKEN');
    }

    /** The connection handle that admin's "Allow" gave the partner. */
    public static function partnerConnection(): string
    {
        return self::env('PUNTJES_PARTNER_CONNECTION');
    }

    /** @return array{0: string, 1: string} two card codes on the shop's card list that no customer holds */
    public static function loyaltyCards(): array
    {
        $codes = explode(',', self::env('PUNTJES_LOYALTY_CARDS'));

        if (count($codes) !== 2) {
            throw new RuntimeException('PUNTJES_LOYALTY_CARDS must hold two card codes, separated by a comma.');
        }

        return [$codes[0], $codes[1]];
    }

    /**
     * Skips a test written before spec/api.json carries its operation; the test runs once the spec has it.
     */
    public static function requireOperation(string $operationId): void
    {
        if (! in_array($operationId, self::operations(), true)) {
            Assert::markTestSkipped("spec/api.json does not carry {$operationId} yet.");
        }
    }

    public static function hit(string $client, string $operationId): void
    {
        self::$hits[$client][$operationId] = true;
    }

    /** @return list<string> */
    public static function missed(string $client): array
    {
        return array_values(array_diff(self::operations(), array_keys(self::$hits[$client] ?? [])));
    }

    /** @return list<string> every operationId in spec/api.json */
    private static function operations(): array
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

        return $all;
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
