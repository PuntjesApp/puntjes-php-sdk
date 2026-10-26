<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ContractCoverageTest extends TestCase
{
    public function test_it_lists_the_operations_no_contract_test_hits_in_code(): void
    {
        [$exit, $uncovered] = self::uncovered(__DIR__.'/../Fixtures/contract-coverage-spec.json', [__DIR__.'/../Fixtures/contract-coverage-suite.txt']);

        self::assertSame(0, $exit);
        self::assertSame(['listProducts', 'createProduct', 'deleteVoucher'], $uncovered);
    }

    public function test_it_refuses_a_spec_it_cannot_read(): void
    {
        [$exit] = self::uncovered(__DIR__.'/../Fixtures/missing.json', [__DIR__.'/../Fixtures/contract-coverage-suite.txt']);

        self::assertSame(1, $exit);
    }

    public function test_every_operation_in_the_spec_has_a_contract_test(): void
    {
        [$exit, $uncovered] = self::uncovered(__DIR__.'/../../spec/api.json', (array) glob(__DIR__.'/../Contract/*.php'));

        self::assertSame(0, $exit);
        self::assertSame([], $uncovered);
    }

    /**
     * @param  list<string>  $suite
     * @return array{0: int, 1: list<string>}
     */
    private static function uncovered(string $spec, array $suite): array
    {
        $command = 'php '.escapeshellarg(__DIR__.'/../../scripts/uncovered-operations.php').' '.escapeshellarg($spec)
            .' '.implode(' ', array_map('escapeshellarg', $suite)).' 2>/dev/null';
        exec($command, $out, $exit);

        return [$exit, $out];
    }
}
