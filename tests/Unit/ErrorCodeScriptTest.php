<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Puntjes\Generated\Errors\ErrorCode;

final class ErrorCodeScriptTest extends TestCase
{
    public function test_it_writes_one_case_per_code_with_its_status(): void
    {
        $out = sys_get_temp_dir().'/ErrorCode-'.bin2hex(random_bytes(4)).'.php';
        exec('php '.escapeshellarg(__DIR__.'/../../scripts/error-codes.php').' '.escapeshellarg(__DIR__.'/../Fixtures/error-codes-spec.json').' '.escapeshellarg($out), $o, $exit);
        $source = (string) file_get_contents($out);

        self::assertSame(0, $exit);
        self::assertStringContainsString("case CustomerNotFound = 'CUSTOMER_NOT_FOUND';", $source);
        self::assertStringContainsString('self::PlanLimitExceeded => 429,', $source);
        self::assertSame(3, substr_count($source, '    case '));
    }

    public function test_the_committed_enum_knows_the_codes_1x_missed(): void
    {
        self::assertSame(422, ErrorCode::from('MARKETING_CONSENT_UNDER_AGE')->status());
        self::assertNotNull(ErrorCode::tryFromString('SUBSCRIPTION_EXPIRED'));
        self::assertNull(ErrorCode::tryFromString('NOT_A_CODE'));
    }
}
