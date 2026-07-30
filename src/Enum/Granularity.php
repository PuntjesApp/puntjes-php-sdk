<?php

declare(strict_types=1);

namespace Puntjes\Enum;

/** Bucket size of the statistics volume trend, derived server-side from the period. */
enum Granularity: string
{
    case Hourly = 'hourly';
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
}
