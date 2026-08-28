<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Exception\ApiException;
use Puntjes\Model\Branch;
use Puntjes\Model\Campaign;
use Puntjes\Pagination\Page;
use Puntjes\Pagination\Paginator;

/** Points-multiplier campaigns. Read-only over the API. */
final class Campaigns extends Resource
{
    /**
     * Active campaigns — both currently running and scheduled to start — newest first.
     *
     * Useful for showing "double points this Friday" at the till. Campaigns are
     * applied server-side when a transaction is recorded; nothing here needs to be
     * passed back in.
     *
     * Each campaign carries the branches it runs at, or null when it runs at every
     * one — see {@see Campaign::$branches}.
     *
     * @param  string|null  $branch  Narrow to campaigns running at one shop, by the vendor's
     *                               branch key. Pass {@see Branch::UNASSIGNED} for the ones
     *                               attributed to no branch. Omitting it is a third thing
     *                               again: every campaign, whatever its scope.
     * @return Paginator<Campaign>
     *
     * @throws ApiException `BRANCH_NOT_FOUND` (422) — the filter is refused rather than
     *                      answered unscoped, because a list that silently covers every
     *                      shop for a mistyped key looks right and is not.
     */
    public function list(?string $branch = null): Paginator
    {
        $query = $branch === null ? [] : ['branch' => $branch];

        return new Paginator(fn (int $page): Page => Page::fromResponse(
            $this->transport->get('/campaigns', $query + ['page' => $page]),
            Campaign::fromArray(...),
        ));
    }
}
