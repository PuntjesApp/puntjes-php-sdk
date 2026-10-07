<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Enum\Period;
use Puntjes\Exception\ApiException;
use Puntjes\Model\Branch;
use Puntjes\Model\Statistics as StatisticsModel;

/**
 * Aggregate sales and loyalty reporting for the authenticated vendor.
 *
 * Under a `branch:` filter the commerce figures cover that shop alone and
 * {@see StatisticsModel::$loyalty} is null — points liability and breakage cannot be
 * attributed to one shop, and answering them vendor-wide beside branch-filtered sales
 * is exactly the misreading the null prevents.
 */
final class Statistics extends Resource
{
    /**
     * Commerce and loyalty figures over one of four preset windows.
     *
     * Arbitrary from/to ranges are an admin-portal feature and are rejected here, so
     * the parameter is an enum rather than a pair of dates.
     *
     * @param  int  $topProductsLimit  How many best-sellers to include, 1–50.
     * @param  string|null  $branch  Report on one shop, by the vendor's branch key. Pass
     *                               {@see Branch::UNASSIGNED} for purchases recorded
     *                               against no branch. Omit it for the whole vendor.
     *
     * @throws ApiException `BRANCH_NOT_FOUND` (422) for a key this vendor has no branch
     *                      for. Refused rather than answered vendor-wide: a report that
     *                      silently widens under a branch label is the one mistake
     *                      nobody catches by reading it. An empty or blank key is
     *                      refused the same way; a Puntjes from before
     *                      PuntjesApp/Puntjes#1084 answered the whole vendor for it.
     */
    public function get(
        Period $period = Period::ThirtyDays,
        int $topProductsLimit = 10,
        ?string $branch = null,
    ): StatisticsModel {
        $query = [
            'period' => $period->value,
            'top_products_limit' => $topProductsLimit,
        ];

        if ($branch !== null) {
            $query['branch'] = $branch;
        }

        return StatisticsModel::fromArray($this->transport->get('/statistics', $query)->dataArray());
    }
}
