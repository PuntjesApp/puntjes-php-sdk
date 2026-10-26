<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ContractCoverageTest extends TestCase
{
    public function test_every_operation_in_the_spec_has_a_contract_test(): void
    {
        $spec = json_decode((string) file_get_contents(__DIR__.'/../../spec/api.json'), true);
        $operations = [];

        foreach ($spec['paths'] as $verbs) {
            foreach ($verbs as $verb => $operation) {
                if (in_array($verb, ['get', 'post', 'put', 'patch', 'delete'], true)) {
                    $operations[] = $operation['operationId'];
                }
            }
        }

        $suite = implode("\n", array_map('file_get_contents', (array) glob(__DIR__.'/../Contract/*.php')));
        preg_match_all("/Live::hit\(self::CLIENT, '([A-Za-z]+)'\)/", $suite, $hits);

        self::assertNotEmpty($hits[1]);
        self::assertSame([], array_values(array_diff($operations, $hits[1])));
    }
}
