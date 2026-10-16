<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use Puntjes\Puntjes;
use Puntjes\Tests\Support\TestCase;
use ReflectionClass;

/**
 * Puntjes\Puntjes only builds clients, so a container asked for it fails at once instead of handing out an empty object.
 */
final class EntryPointTest extends TestCase
{
    public function test_the_entry_point_cannot_be_built_by_a_container(): void
    {
        $class = new ReflectionClass(Puntjes::class);

        self::assertFalse($class->isInstantiable());
        self::assertTrue($class->getConstructor()?->isPrivate());
    }
}
