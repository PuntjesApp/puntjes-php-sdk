<?php

declare(strict_types=1);

namespace Puntjes\Enum;

/**
 * Reporting windows accepted by `GET /statistics`.
 *
 * Windows are computed on the Europe/Brussels business calendar and returned as
 * UTC boundaries. Arbitrary from/to ranges are an admin-portal feature and are
 * rejected here.
 */
enum Period: string
{
    case Today = 'today';
    case SevenDays = '7d';
    case ThirtyDays = '30d';
    case NinetyDays = '90d';
}
