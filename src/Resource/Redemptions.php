<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Enum\RedemptionStatus;
use Puntjes\Exception\ApiException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Model\Redemption;
use Puntjes\Pagination\Page;
use Puntjes\Pagination\Paginator;
use Puntjes\Request\CreateRedemption;

/**
 * Exchanging points for rewards, and handing the resulting code in.
 *
 * The flow is two-step by design: `create()` deducts the points and issues a
 * confirmation code, `verify()` marks that code used when the customer actually
 * collects. A code can be verified exactly once.
 */
final class Redemptions extends Resource
{
    /**
     * Redeem a reward, deducting the points and issuing a confirmation code. Responds 201.
     *
     * Safe to retry: replaying the idempotency key returns the original redemption
     * without deducting again. A replay returns the original redemption also when the shop
     * cancelled it later, and that answer has no status: call `find()` to read the current
     * status. Reusing a key for a DIFFERENT customer or reward is
     * rejected with `IDEMPOTENCY_KEY_CONFLICT` rather than returning the unrelated
     * redemption that key belongs to.
     *
     * @throws ApiException `INSUFFICIENT_BALANCE`, `OUT_OF_STOCK`,
     *                      `REWARD_UNAVAILABLE`, `NO_WALLET` or
     *                      `CUSTOMER_DEACTIVATED` — all 422.
     * @throws ApiException `BRANCH_REQUIRED` (422) when the reward is limited to
     *                      branches and this is not one of them, or `BRANCH_NOT_FOUND`
     *                      when the key names no branch of this vendor.
     * @throws NotFoundException `CUSTOMER_NOT_FOUND` or `REWARD_NOT_FOUND`.
     */
    public function create(CreateRedemption $redemption): Redemption
    {
        return Redemption::fromArray(
            $this->transport->post('/redemptions', $redemption->toArray())->dataArray(),
        );
    }

    /**
     * Look a confirmation code up without consuming it — the "what is this code
     * worth?" call for a member of staff.
     *
     * A `valid` code past its expiry is changed to `expired` as a side effect of being
     * read, so the status returned is always current. A used or cancelled code keeps its status.
     */
    public function find(string $confirmationCode): Redemption
    {
        return Redemption::fromArray(
            $this->transport->get('/redemptions/'.$this->segment($confirmationCode))->dataArray(),
        );
    }

    /**
     * A customer's redemptions, newest first, 15 per page: the call for a till whose
     * customer comes to collect a reward without the confirmation code.
     *
     * Pass `RedemptionStatus::Valid` for the rewards still to collect, then `verify()`
     * the one the customer picks. Each status is the one the code has at the moment
     * of the request, so a `valid` code past its expiry reads `expired` and is left out of
     * `Valid` even before anyone looked it up. A used or cancelled code keeps its status. The list only reads; it never writes.
     *
     * @return Paginator<Redemption>
     *
     * @throws NotFoundException `CUSTOMER_NOT_FOUND`, also for a customer of another vendor.
     */
    public function forCustomer(int $customerId, ?RedemptionStatus $status = null): Paginator
    {
        $query = $status === null ? [] : ['status' => $status->value];
        $path = '/customers/'.$this->segment($customerId).'/redemptions';

        return new Paginator(fn (int $page): Page => Page::fromResponse(
            $this->transport->get($path, $query + ['page' => $page]),
            Redemption::fromArray(...),
        ));
    }

    /**
     * Mark a code used, at the moment the customer collects.
     *
     * NOT retried automatically: a second call answers `CODE_ALREADY_USED`, so an
     * automatic replay of a request whose response was merely lost would look like a
     * failure. Handle that code as "already collected" if you retry yourself.
     *
     * A cancelled code answers `CODE_CANCELLED`: the shop cancelled the redemption in the admin
     * portal and the customer got the points back, so the till must not hand over the reward.
     *
     * @throws ApiException `CODE_ALREADY_USED`, `CODE_EXPIRED` or `CODE_CANCELLED` (422).
     * @throws NotFoundException `REDEMPTION_NOT_FOUND`.
     */
    public function verify(string $confirmationCode): Redemption
    {
        return Redemption::fromArray(
            $this->transport->post('/redemptions/'.$this->segment($confirmationCode).'/verify')->dataArray(),
        );
    }
}
