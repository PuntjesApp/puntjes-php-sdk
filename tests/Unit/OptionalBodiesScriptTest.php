<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class OptionalBodiesScriptTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir().'/optional-bodies-'.bin2hex(random_bytes(4));
        mkdir($this->dir.'/Verify', recursive: true);
        copy(__DIR__.'/../Fixtures/optional-bodies-builder.txt', $this->dir.'/Verify/VerifyRequestBuilder.php');
    }

    public function test_a_body_that_is_not_required_becomes_optional(): void
    {
        [$exit] = $this->runScript(['/vouchers/{code}/verify' => ['post' => ['requestBody' => []]]]);
        $source = $this->builder();

        self::assertSame(0, $exit);
        self::assertStringContainsString('public function post(?VerifyCampaignVoucherData $body = null, ', $source);
        self::assertStringContainsString('public function toPostRequestInformation(?VerifyCampaignVoucherData $body = null, ', $source);
        self::assertSame(2, substr_count($source, '@param VerifyCampaignVoucherData|null $body The request body'));
        self::assertStringContainsString("        if (\$body !== null) {\n            \$requestInfo->setContentFromParsable(", $source);
    }

    public function test_a_required_body_stays_required(): void
    {
        [$exit] = $this->runScript(['/vouchers/{code}/verify' => ['post' => ['requestBody' => ['required' => true]]]]);

        self::assertSame(0, $exit);
        self::assertSame((string) file_get_contents(__DIR__.'/../Fixtures/optional-bodies-builder.txt'), $this->builder());
    }

    public function test_a_builder_of_an_unexpected_shape_fails(): void
    {
        file_put_contents($this->dir.'/Verify/VerifyRequestBuilder.php', str_replace('setContentFromParsable', 'setBody', $this->builder()));

        [$exit, $error] = $this->runScript(['/vouchers/{code}/verify' => ['post' => ['requestBody' => []]]]);

        self::assertSame(1, $exit);
        self::assertStringContainsString('POST /vouchers/{code}/verify', $error);
    }

    public function test_an_operation_without_exactly_one_builder_fails(): void
    {
        [$exit, $error] = $this->runScript(['/redemptions/{code}/verify' => ['post' => ['requestBody' => []]]]);

        self::assertSame(1, $exit);
        self::assertStringContainsString('expected one request builder, found 0', $error);
    }

    /**
     * @param  array<string, mixed>  $paths
     * @return array{int, string}
     */
    private function runScript(array $paths): array
    {
        $spec = $this->dir.'/spec.json';
        file_put_contents($spec, (string) json_encode(['paths' => $paths]));
        $process = proc_open(
            ['php', __DIR__.'/../../scripts/optional-bodies.php', $spec, $this->dir],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
        );
        self::assertIsResource($process);
        stream_get_contents($pipes[1]);
        $error = (string) stream_get_contents($pipes[2]);

        return [proc_close($process), $error];
    }

    private function builder(): string
    {
        return (string) file_get_contents($this->dir.'/Verify/VerifyRequestBuilder.php');
    }
}
