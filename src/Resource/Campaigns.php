<?php

declare(strict_types=1);

namespace Puntjes\Resource;

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
     * @return Paginator<Campaign>
     */
    public function list(): Paginator
    {
        return new Paginator(fn (int $page): Page => Page::fromResponse(
            $this->transport->get('/campaigns', ['page' => $page]),
            Campaign::fromArray(...),
        ));
    }
}
