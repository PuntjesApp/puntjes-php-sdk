<?php

declare(strict_types=1);

namespace Puntjes\Spike\Tests;

use PHPUnit\Framework\TestCase;
use Puntjes\Spike\Tests\Support\Live;
use RuntimeException;

final class LocalGuardTest extends TestCase
{
    public function test_it_refuses_the_production_api(): void
    {
        $this->expectException(RuntimeException::class);

        Live::assertLocal('https://puntjes.app/api/v1');
    }

    public function test_it_refuses_a_lookalike_host(): void
    {
        $this->expectException(RuntimeException::class);

        Live::assertLocal('https://localhost.puntjes.app/api/v1');
    }

    public function test_it_accepts_the_docker_app(): void
    {
        Live::assertLocal('http://laravel.test/api/v1');

        $this->addToAssertionCount(1);
    }
}
