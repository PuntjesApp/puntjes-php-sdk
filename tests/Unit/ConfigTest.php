<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Puntjes\Config;
use Puntjes\Exception\ConfigurationException;

final class ConfigTest extends TestCase
{
    /**
     * The API documentation states the base URL as `{APP_URL}/api/v1`, so that is the
     * string integrators paste. The bare host has to work too, because that is what
     * "base URL" means in most other SDKs.
     *
     * @return array<string, array{string}>
     */
    public static function equivalentBaseUrls(): array
    {
        return [
            'as documented' => ['https://puntjes.app/api/v1'],
            'bare host' => ['https://puntjes.app'],
            'documented, trailing slash' => ['https://puntjes.app/api/v1/'],
            'bare host, trailing slash' => ['https://puntjes.app/'],
            'surrounding whitespace' => ['  https://puntjes.app/api/v1  '],
        ];
    }

    /** @dataProvider equivalentBaseUrls */
    public function test_every_accepted_base_url_form_resolves_identically(string $baseUrl): void
    {
        $config = new Config('id', 'secret', $baseUrl);

        self::assertSame('https://puntjes.app', $config->baseUrl);
        self::assertSame('https://puntjes.app/api/v1/customers/lookup', $config->apiUrl('/customers/lookup'));

        // The token endpoint hangs off the root, NOT off /api/v1. Storing the documented
        // base URL verbatim would look for it at /api/v1/oauth/token and 404 forever.
        self::assertSame('https://puntjes.app/oauth/token', $config->tokenUrl());
    }

    public function test_api_paths_join_correctly_with_or_without_a_leading_slash(): void
    {
        $config = new Config('id', 'secret', 'https://puntjes.app/api/v1');

        self::assertSame('https://puntjes.app/api/v1/me', $config->apiUrl('/me'));
        self::assertSame('https://puntjes.app/api/v1/me', $config->apiUrl('me'));
    }

    public function test_a_host_whose_own_path_ends_in_api_v1_is_not_over_stripped(): void
    {
        // Only one occurrence of the suffix is removed, so an instance genuinely mounted
        // under a subdirectory keeps it.
        $config = new Config('id', 'secret', 'https://example.test/puntjes/api/v1');

        self::assertSame('https://example.test/puntjes', $config->baseUrl);
        self::assertSame('https://example.test/puntjes/api/v1/me', $config->apiUrl('/me'));
        self::assertSame('https://example.test/puntjes/oauth/token', $config->tokenUrl());
    }

    public function test_a_local_http_host_with_a_port_is_accepted(): void
    {
        $config = new Config('id', 'secret', 'http://localhost:8080/api/v1');

        self::assertSame('http://localhost:8080/api/v1/health', $config->apiUrl('/health'));
        self::assertSame('http://localhost:8080/oauth/token', $config->tokenUrl());
    }

    /** @return array<string, array{string, string}> */
    public static function invalidSettings(): array
    {
        return [
            'empty client id' => ['', 'secret'],
            'empty client secret' => ['id', ''],
        ];
    }

    /** @dataProvider invalidSettings */
    public function test_missing_credentials_are_rejected_up_front(string $id, string $secret): void
    {
        $this->expectException(ConfigurationException::class);

        new Config($id, $secret, 'https://puntjes.app/api/v1');
    }

    public function test_an_unusable_base_url_is_rejected_up_front(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('not a valid Puntjes base URL');

        new Config('id', 'secret', 'not a url');
    }

    public function test_negative_retries_are_rejected(): void
    {
        $this->expectException(ConfigurationException::class);

        new Config('id', 'secret', 'https://puntjes.app', maxRetries: -1);
    }

    public function test_the_credential_fingerprint_never_contains_the_secret(): void
    {
        $fingerprint = (new Config('my-client', 'super-secret', 'https://puntjes.app'))->credentialFingerprint();

        self::assertStringNotContainsString('super-secret', $fingerprint);
        self::assertStringNotContainsString('my-client', $fingerprint);
        self::assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $fingerprint);
    }

    public function test_the_fingerprint_distinguishes_hosts_as_well_as_credentials(): void
    {
        // Same credentials against staging and production must not share a cached token.
        $production = new Config('id', 'secret', 'https://puntjes.app');
        $staging = new Config('id', 'secret', 'https://staging.puntjes.app');

        self::assertNotSame($production->credentialFingerprint(), $staging->credentialFingerprint());
    }

    public function test_the_two_accepted_base_url_forms_share_one_fingerprint(): void
    {
        // Otherwise changing PUNTJES_BASE_URL between the two equivalent spellings would
        // silently orphan the cached token and force a re-grant.
        self::assertSame(
            (new Config('id', 'secret', 'https://puntjes.app/api/v1'))->credentialFingerprint(),
            (new Config('id', 'secret', 'https://puntjes.app'))->credentialFingerprint(),
        );
    }
}
