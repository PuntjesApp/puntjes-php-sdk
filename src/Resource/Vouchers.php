<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Exception\ApiException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Model\Branch;
use Puntjes\Model\VoucherVerification;

/**
 * Campaign bonnen — the discount and free-product vouchers a campaign hands out.
 *
 * Distinct from {@see Redemptions}, which spends a customer's points. A bon is
 * something the vendor gave away, so there is no balance and no reward catalogue
 * involved: the customer arrives holding a code, and the till spends it.
 */
final class Vouchers extends Resource
{
    /**
     * Spend a bon at the till.
     *
     * This is a consume, not a preview — a successful call marks the bon used, and a
     * second call answers `VOUCHER_ALREADY_USED`. There is no way to ask "is this code
     * good?" without spending it, deliberately: a preview that could be replayed is how
     * one bon gets honoured twice.
     *
     * For that reason it is never retried automatically. If you retry it yourself,
     * treat `VOUCHER_ALREADY_USED` as "possibly my own earlier attempt" rather than as
     * a customer trying it on.
     *
     * A code belonging to another vendor answers exactly what a code that was never
     * issued answers, byte for byte — so `VOUCHER_NOT_FOUND` never confirms that a code
     * exists somewhere else.
     *
     * @param  string  $code  The code from the customer's QR or printed bon.
     * @param  string|null  $branch  Where it is being spent, by the vendor's branch key.
     *                               Falls back to the branch this API credential defaults
     *                               to. {@see Branch::UNASSIGNED} is a filter word and is
     *                               not valid here.
     *
     * @throws NotFoundException `VOUCHER_NOT_FOUND` (404).
     * @throws ApiException `VOUCHER_ALREADY_USED`, `VOUCHER_EXPIRED`,
     *                      `BRANCH_REQUIRED` or `BRANCH_NOT_FOUND` — all 422.
     */
    public function verify(string $code, ?string $branch = null): VoucherVerification
    {
        $body = $branch === null ? [] : ['branch' => $branch];

        return VoucherVerification::fromArray(
            $this->transport->post('/vouchers/'.$this->segment($code).'/verify', $body)->dataArray(),
        );
    }
}
