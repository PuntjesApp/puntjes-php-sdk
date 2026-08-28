<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Model\RewardSummary;

/** The reward catalogue. */
final class Rewards extends Resource
{
    /**
     * Active rewards, cheapest first.
     *
     * Not paginated — the whole catalogue comes back in one call.
     *
     * Each reward carries the branches it may be redeemed at, or null for anywhere —
     * see {@see RewardSummary::$branches}. A till serving one shop should check that
     * before offering a reward, rather than discovering `BRANCH_REQUIRED` at redemption.
     *
     * @param  string|null  $affordableFor  A customer's loyalty identifier. When given, the
     *                                      list is filtered to what that customer can afford
     *                                      right now, which is what a till should display.
     * @return array<int, RewardSummary>
     */
    public function list(?string $affordableFor = null): array
    {
        $query = $affordableFor === null
            ? []
            : ['affordable' => true, 'identifier' => $affordableFor];

        $rewards = [];

        // Unlike the paginated endpoints, `data` here is the bare list.
        foreach ($this->transport->get('/rewards', $query)->dataArray() as $row) {
            if (is_array($row)) {
                $rewards[] = RewardSummary::fromArray($row);
            }
        }

        return $rewards;
    }
}
