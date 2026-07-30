<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Exception\ApiException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Exception\PlanLimitExceededException;
use Puntjes\Model\Transaction;
use Puntjes\Pagination\Page;
use Puntjes\Pagination\Paginator;
use Puntjes\Request\DateRangeFilters;
use Puntjes\Request\SubmitTransaction;

/** Recording purchases and reading a customer's purchase history. */
final class Transactions extends Resource
{
    /**
     * Record a purchase and award the points its earn rules produce. Responds 201.
     *
     * Safe to retry: the request carries an idempotency key, so a replay returns the
     * original transaction rather than awarding points twice. Note that the replayed
     * response reports `pointsEarned: 0` — the points from the first call still
     * stand. Read the wallet if you need the balance.
     *
     * @throws NotFoundException (`CUSTOMER_NOT_FOUND`) for an unknown identifier.
     * @throws ApiException (`CUSTOMER_DEACTIVATED`, 422) when the customer cannot transact.
     * @throws PlanLimitExceededException when the vendor's plan transaction cap is spent.
     */
    public function submit(SubmitTransaction $transaction): Transaction
    {
        return Transaction::fromArray(
            $this->transport->post('/transactions', $transaction->toArray())->dataArray(),
        );
    }

    /**
     * A customer's purchase history, newest first, 15 per page.
     *
     * @return Paginator<Transaction>
     */
    public function forCustomer(int $customerId, ?DateRangeFilters $filters = null): Paginator
    {
        $query = $filters?->toQuery() ?? [];
        $path = '/customers/'.$this->segment($customerId).'/transactions';

        return new Paginator(fn (int $page): Page => Page::fromResponse(
            $this->transport->get($path, $query + ['page' => $page]),
            Transaction::fromArray(...),
        ));
    }
}
