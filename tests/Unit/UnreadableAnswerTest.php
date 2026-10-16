<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Exception\TransportException;
use Puntjes\Tests\Support\TestCase;

/**
 * The message of an answer the SDK cannot read says whether it was not JSON or JSON that could not be read.
 */
final class UnreadableAnswerTest extends TestCase
{
    public function test_a_json_answer_that_cannot_be_read_says_so_and_names_the_cause(): void
    {
        $this->fake->queueRaw(200, '{"data": [', ['Content-Type' => 'application/json']);

        try {
            $this->puntjes()->rewards()->get()->wait();
            self::fail('No exception');
        } catch (TransportException $e) {
            self::assertStringStartsWith('Could not read the JSON response of the Puntjes API (HTTP 200): ', $e->getMessage());
            self::assertStringContainsString((string) $e->getPrevious()?->getMessage(), $e->getMessage());
            self::assertStringNotContainsString('Expected a JSON response', $e->getMessage());
        }
    }

    public function test_an_answer_that_is_not_json_keeps_the_expected_json_message(): void
    {
        $this->fake->queueRaw(200, '<html>maintenance</html>', ['Content-Type' => 'text/html']);

        try {
            $this->puntjes()->rewards()->get()->wait();
            self::fail('No exception');
        } catch (TransportException $e) {
            self::assertStringStartsWith('Expected a JSON response from the Puntjes API but got text/html (HTTP 200): ', $e->getMessage());
        }
    }
}
