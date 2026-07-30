<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Enum\Period;
use Puntjes\Model\Statistics as StatisticsModel;

/** Aggregate sales and loyalty reporting for the authenticated vendor. */
final class Statistics extends Resource
{
    /**
     * Commerce and loyalty figures over one of four preset windows.
     *
     * Arbitrary from/to ranges are an admin-portal feature and are rejected here, so
     * the parameter is an enum rather than a pair of dates.
     *
     * @param  int  $topProductsLimit  How many best-sellers to include, 1–50.
     */
    public function get(Period $period = Period::ThirtyDays, int $topProductsLimit = 10): StatisticsModel
    {
        return StatisticsModel::fromArray($this->transport->get('/statistics', [
            'period' => $period->value,
            'top_products_limit' => $topProductsLimit,
        ])->dataArray());
    }
}
