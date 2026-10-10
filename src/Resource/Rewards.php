<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Exception\ConfigurationException;
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
     * A customer named by either identifier also fills {@see RewardSummary::$customerRedemptions},
     * so a till can grey out a reward the customer may not redeem again.
     *
     * @param  string|null  $affordableFor  A customer's loyalty identifier. When given, the
     *                                      list is filtered to what that customer can afford
     *                                      right now, which is what a till should display.
     * @param  string|null  $countRedemptionsFor  A customer's loyalty identifier. When given, each reward
     *                                            says how often that customer redeemed it, without the
     *                                            filter. {@see RewardSummary::redemptionsLeft()}
     * @return array<int, RewardSummary>
     *
     * @throws ConfigurationException when the two identifiers name different customers.
     */
    public function list(?string $affordableFor = null, ?string $countRedemptionsFor = null): array
    {
        if ($affordableFor !== null && $countRedemptionsFor !== null && $affordableFor !== $countRedemptionsFor) {
            throw new ConfigurationException('The reward catalogue counts the redemptions of one customer: the customer of the affordable filter.');
        }

        $query = match (true) {
            $affordableFor !== null => ['affordable' => true, 'identifier' => $affordableFor],
            $countRedemptionsFor !== null => ['identifier' => $countRedemptionsFor],
            default => [],
        };

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
