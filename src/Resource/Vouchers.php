<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Exception\ApiException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Model\Branch;
use Puntjes\Model\OpenVoucher;
use Puntjes\Model\VoucherLookup;
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
     * The bons a customer can still spend: the call for a till whose customer comes
     * without the code.
     *
     * One plain list, not paginated. The bon that runs out first comes first and a bon
     * that never runs out comes last. A bon that was spent, or whose last day has
     * passed, is left out; a bon stays in for the whole of its last day, Belgian time.
     * Show the list, let the customer pick, then spend that bon with {@see verify()}.
     * {@see OpenVoucher::isSpendableAt()} tells whether this shop takes it.
     *
     * A deactivated customer still gets their list, because their bons can still be spent.
     *
     * @param  int  $customerId  The Puntjes customer id, as {@see Customers::lookup()} returns it.
     * @return array<int, OpenVoucher>
     *
     * @throws NotFoundException `CUSTOMER_NOT_FOUND`, also for a customer of another vendor.
     */
    public function forCustomer(int $customerId): array
    {
        $vouchers = [];

        foreach ($this->transport->get('/customers/'.$this->segment($customerId).'/vouchers')->dataArray() as $row) {
            if (is_array($row)) {
                $vouchers[] = OpenVoucher::fromArray($row);
            }
        }

        return $vouchers;
    }

    /**
     * Read a bon without spending it: the "is this code good?" call for the till.
     *
     * The bon stays as it was. An expired bon answers with `VoucherStatus::Expired`, not
     * with an error, so the till can tell the customer why. Read the status, then spend
     * the bon with {@see verify()}. A bon can change between the two calls, so `verify()`
     * stays the only check that counts.
     *
     * A code of another vendor answers exactly as a code that was never issued, so
     * `VOUCHER_NOT_FOUND` never confirms that a code exists somewhere else.
     *
     * @param  string  $code  The code from the customer's QR or printed bon.
     *
     * @throws NotFoundException `VOUCHER_NOT_FOUND` (404).
     */
    public function find(string $code): VoucherLookup
    {
        return VoucherLookup::fromArray(
            $this->transport->get('/vouchers/'.$this->segment($code))->dataArray(),
        );
    }

    /**
     * Spend a bon at the till.
     *
     * This is a consume, not a preview. A successful call marks the bon used. To read a
     * bon without spending it, use {@see find()}.
     *
     * Without an idempotency key, the SDK never retries this call automatically, and a
     * second call answers `VOUCHER_ALREADY_USED`. If you retry it yourself, treat
     * `VOUCHER_ALREADY_USED` as "possibly my own earlier attempt", not as a customer who
     * tries the bon again.
     *
     * With an idempotency key, the SDK retries a failed call automatically, as it does
     * for every POST that carries a key. A repeat with the same key on the same bon
     * answers the first success again. Make the key from something stable in your
     * system, for example the sale. The SDK does not make a key for you. Two refusals
     * are possible:
     *
     * - the same key on another bon answers `IDEMPOTENCY_KEY_CONFLICT`, and that other
     *   bon stays unspent;
     * - a key sent after an earlier spend without a key answers `VOUCHER_ALREADY_USED`,
     *   because the first spend has no key to match.
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
     * @param  string|null  $idempotencyKey  Up to 255 characters. Sent only when you give one.
     *
     * @throws NotFoundException `VOUCHER_NOT_FOUND` (404).
     * @throws ApiException `VOUCHER_ALREADY_USED`, `VOUCHER_EXPIRED`,
     *                      `IDEMPOTENCY_KEY_CONFLICT`, `BRANCH_REQUIRED` or
     *                      `BRANCH_NOT_FOUND`, or `VALIDATION_ERROR` for a key longer than
     *                      255 characters — all 422.
     */
    public function verify(string $code, ?string $branch = null, ?string $idempotencyKey = null): VoucherVerification
    {
        $body = $branch === null ? [] : ['branch' => $branch];

        if ($idempotencyKey !== null) {
            $body['idempotency_key'] = $idempotencyKey;
        }

        return VoucherVerification::fromArray(
            $this->transport->post('/vouchers/'.$this->segment($code).'/verify', $body)->dataArray(),
        );
    }
}
