<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Enum\WalletPassPlatform;
use Puntjes\Exception\ApiException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Exception\TransportException;
use Puntjes\Model\LedgerEntry;
use Puntjes\Model\Wallet;
use Puntjes\Pagination\Page;
use Puntjes\Pagination\Paginator;
use Puntjes\Request\AdjustWallet;
use Puntjes\Request\DateRangeFilters;

/** Balances, the points ledger, manual corrections, and wallet passes. */
final class Wallets extends Resource
{
    /**
     * The customer's current balance, plus how many points expire within 30 days.
     *
     * @throws NotFoundException `CUSTOMER_NOT_FOUND` or `WALLET_NOT_FOUND`.
     */
    public function show(int $customerId): Wallet
    {
        return Wallet::fromArray(
            $this->transport->get('/customers/'.$this->segment($customerId).'/wallet')->dataArray(),
        );
    }

    /**
     * The append-only points ledger, newest first, 15 per page.
     *
     * Filter by entry type with `new DateRangeFilters(type: 'earn')`.
     *
     * @return Paginator<LedgerEntry>
     */
    public function ledger(int $customerId, ?DateRangeFilters $filters = null): Paginator
    {
        $query = $filters?->toQuery() ?? [];
        $path = '/customers/'.$this->segment($customerId).'/ledger';

        return new Paginator(fn (int $page): Page => Page::fromResponse(
            $this->transport->get($path, $query + ['page' => $page]),
            LedgerEntry::fromArray(...),
        ));
    }

    /**
     * Credit or debit points manually, returning the resulting ledger entry.
     *
     * Safe to retry: replaying the idempotency key returns the original entry
     * without moving the balance again. The key also matches an adjustment made on an
     * account the shop merged into this customer. Such a replay returns that account's
     * entry, with its own `walletId` and `runningBalance`, not this customer's balance.
     *
     * A customer the shop merged into another account answers `CUSTOMER_DEACTIVATED`
     * (422) for a new key; a key used before the merge still returns its entry. A
     * deactivated customer the shop did not merge keeps today's answers.
     *
     * @throws ApiException (`INSUFFICIENT_BALANCE`, 422) when a debit exceeds the balance,
     *                      (`IDEMPOTENCY_KEY_CONFLICT`, 422) when the key was used with
     *                      another amount, and (`CUSTOMER_DEACTIVATED`, 422) for a merged
     *                      customer.
     */
    public function adjust(int $customerId, AdjustWallet $adjustment): LedgerEntry
    {
        return LedgerEntry::fromArray(
            $this->transport->post(
                '/customers/'.$this->segment($customerId).'/wallet/adjust',
                $adjustment->toArray(),
            )->dataArray(),
        );
    }

    /**
     * The signed Apple Wallet pass, as raw `application/vnd.apple.pkpass` bytes.
     *
     * **Experimental** — the wallet-pass feature is not yet fully integrated on the
     * Puntjes side; expect this endpoint to change. Verify it against your target
     * environment before shipping, and give your HTTP client a request timeout: an
     * instance whose pass integration is incomplete can hang rather than error.
     *
     * The only endpoint that does not answer JSON. Serve the bytes with that content
     * type and a `.pkpass` filename; iOS opens Wallet from there. The balance is
     * rendered fresh on every call, so passes are never stale.
     *
     * @throws ApiException (`CUSTOMER_DEACTIVATED`, 422) when the shop merged this customer
     *                      into another account. A deactivated customer the shop did not
     *                      merge still gets a pass.
     */
    public function applePass(int $customerId): string
    {
        return $this->transport->get(
            '/customers/'.$this->segment($customerId).'/wallet-pass',
            ['platform' => WalletPassPlatform::Apple->value],
        )->body;
    }

    /**
     * The Google Wallet save URL to redirect the customer to.
     *
     * **Experimental** — same caveat as {@see applePass()}: the wallet-pass feature
     * is not yet fully integrated on the Puntjes side. Verify before shipping and
     * configure a client timeout.
     *
     * Google returns a link rather than a file — this is a URL to send the customer
     * to, not a redirect the SDK follows.
     *
     * @throws ApiException (`CUSTOMER_DEACTIVATED`, 422) when the shop merged this customer
     *                      into another account, as for {@see applePass()}.
     * @throws TransportException when the response carries no usable save URL, so a
     *                            caller can never end up redirecting a customer to "".
     */
    public function googlePassUrl(int $customerId): string
    {
        $data = $this->transport->get(
            '/customers/'.$this->segment($customerId).'/wallet-pass',
            ['platform' => WalletPassPlatform::Google->value],
        )->dataArray();

        $saveUrl = $data['save_url'] ?? null;

        if (! is_string($saveUrl) || $saveUrl === '') {
            throw new TransportException(
                'The Puntjes API returned no save_url for the Google wallet pass — the wallet-pass integration may not be configured on this instance.',
            );
        }

        return $saveUrl;
    }
}
