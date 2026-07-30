<?php

declare(strict_types=1);

namespace Puntjes\Model\Statistics;

use Puntjes\Enum\Granularity;
use Puntjes\Enum\Period;
use Puntjes\Support\Cast;

/**
 * The window the figures were computed over.
 *
 * Boundaries come back as ISO-8601 UTC, but the window itself is cut on the
 * Europe/Brussels business calendar — so "today" means the Belgian trading day, not
 * a UTC day. Read {@see $from}/{@see $to} rather than recomputing the range locally.
 */
final class StatisticsPeriod
{
    public function __construct(
        public readonly ?Period $preset,
        public readonly string $from,
        public readonly string $to,
        public readonly string $timezone,
        public readonly ?Granularity $granularity,
    ) {}

    /** @param array<array-key, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            preset: Period::tryFrom(Cast::string($data, 'preset')),
            from: Cast::string($data, 'from'),
            to: Cast::string($data, 'to'),
            timezone: Cast::string($data, 'timezone'),
            granularity: Granularity::tryFrom(Cast::string($data, 'granularity')),
        );
    }
}
