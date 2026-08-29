# Changelog

Notable changes to `puntjes/php-sdk`. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this package follows
[semantic versioning](https://semver.org/). From 1.0.0 that promise is the ordinary one:
a breaking change waits for the next major, so `^1.0` is safe to pin and leave.

## 1.0.0 — 2026-08-29

The first stable release, and it carries one fix worth the version on its own.

Fixes the customer registration contract, which had been wrong since the API collapsed
its identifier types. **This release breaks `IdentifierType` and the `CreateIdentifier`
factories.** Both changes are mechanical, and the old spellings could not have worked.

Leaving Beta is the other half. Every endpoint on `routes/api/v1.php` is covered, the
response models are checked field by field against the API's own Data classes, and the
identifier defect below was the last known disagreement between the two. Pin `^1.0` and
a breaking change will not reach you without a major bump.

### Fixed

- **A loyalty card read from the API no longer decodes with a null `type`.** The enum
  had no `loyalty_card` case, so `IdentifierType::tryFrom()` answered null for the type
  the API issues on every customer. Code filtering identifiers by type found nothing and
  raised nothing, which is the worst shape a defect can take. **This is the change worth
  upgrading for on its own**, and it needs no edit on your side.
- **The documented loyalty-card format was wrong.** The README showed `PNTJ-...`, which
  is the prefix on a redemption confirmation code, not a card. A card code is 8 characters
  of `A-Z0-9` with no prefix. The README now also states the rule that will otherwise
  surprise you: a `loyalty_card` value you submit must match a card the vendor has printed
  and not yet assigned, or the API answers `LOYALTY_CARD_NOT_FOUND` (422) or
  `IDENTIFIER_DUPLICATE` (409).
- **A customer can be registered with no identifiers.** `CreateCustomer` threw when
  `identifiers` was empty, while the API treats the empty array as the normal case and
  issues the loyalty card itself. The recommended registration flow was unreachable
  through this SDK.

### Changed

- `IdentifierType` now carries `Email`, `LoyaltyCard` and a deprecated read-only `Phone`.
  `Card`, `Qr`, `Nfc` and `Barcode` are gone: the API removed those values, nothing
  stores them, and submitting one has been a 422 for some time.
- `CreateIdentifier::card()` is replaced by `CreateIdentifier::loyaltyCard()`.
- `CreateIdentifier::phone()` is removed. Phone identifiers cannot be registered.
- `CreateCustomer::$identifiers` defaults to `[]` and no longer throws when empty. The
  parameter keeps its position, so positional callers are unaffected.

### Upgrading

Rename the factory and the enum case. There is no behavioural change to absorb, because
neither old spelling reached a successful request:

```diff
-CreateIdentifier::card('PNTJ-1')
+CreateIdentifier::loyaltyCard('PNTJ-1')

-new CreateIdentifier(IdentifierType::Card, 'PNTJ-1')
+new CreateIdentifier(IdentifierType::LoyaltyCard, 'PNTJ-1')
```

If you called `CreateIdentifier::phone()`, drop the call. Pass the number as
`CreateCustomer(phone: ...)` instead, which is a customer attribute rather than an
identifier.

If you relied on the empty-identifiers exception as a validation step, that check is
yours to keep on your own side.

## 0.2.0 — 2026-08-28

Catches the SDK up with the API as of 2026-08-28. The headline is **branches**: a
vendor can now run several shops, and a purchase, a redemption or a bon records which
one it happened at.

### Fixed

- **`Campaign::$multiplier`, `$recurrenceType` and `$recurrenceConfig` are now
  nullable.** A customer-moment campaign — a birthday gift, say — multiplies nothing
  and recurs on nothing, and the API has been sending null for all three. The previous
  non-nullable types made `campaigns->list()` throw a `TypeError` for any vendor
  running one. **This is the one change worth upgrading for on its own.**
- **`Statistics::$loyalty` is now nullable**, which it must be before the new `branch:`
  filter can be used at all: a branch-filtered report carries no loyalty block.

### Added

- **Branches.**
  - `SubmitTransaction(branch: …)`, `CreateRedemption(branch: …)` and
    `Vouchers::verify(branch: …)` name the shop a write happened at. Omit it and the
    API falls back to the branch your API credential defaults to, then to none.
  - `Statistics::get(branch: …)` and `Campaigns::list(branch: …)` narrow a report to
    one shop. `Branch::UNASSIGNED` (`'none'`) selects what was recorded against no
    branch — a third state, distinct from omitting the filter entirely. It is a filter
    word only and is never valid on a write.
  - `Transaction::$branch` reports where a purchase was rung up, or null for the
    Unassigned bucket.
  - `RewardSummary::$branches` and `Campaign::$branches` carry the shops a reward or
    campaign is limited to. `null` means everywhere; an empty array means the scope
    named branches that have all since been deleted, so it now matches nothing. The two
    are never collapsed. `isRedeemableEverywhere()` and `runsEverywhere()` read it.
  - New `Puntjes\Model\Branch` and `Puntjes\Enum\BranchType`.
- **`$puntjes->vouchers->verify()`** — spend a campaign bon at the till
  (`POST /vouchers/{code}/verify`). A consume, not a preview: a successful call marks
  the bon used. Handles both kinds behind one shape — read `isFreeProduct()`, then
  either `$result->products` or `$result->discount->appliedTo($orderTotal)`.
  New models `VoucherVerification`, `VoucherDiscount`, `VoucherProduct`.
- **`$puntjes->customers->linkExternalId()`** — attach your own id to a customer who
  registered before your integration existed, found by a card code or email they
  already carry (`POST /customers/link-external-id`). Re-sending the same id succeeds;
  a different one is refused with `CUSTOMER_ALREADY_LINKED` rather than overwriting.
- **`$puntjes->customers->sendCard()` and `sendCardByExternalId()`** — email a customer
  their loyalty card. Responds 202; never auto-retried, since a replay mails them
  twice.
- **Customer fields:** `$customerSince`, `$loyaltyCardCode`, and `$marketingConsent`
  (a `MarketingConsent` object carrying the decided answer plus when and where consent
  was granted or withdrawn). `Customer::hasMarketingConsent()` is the short form.
- **`CreateCustomer(customerSince:, marketingConsent:)`** and
  **`UpdateCustomer(customerSince:, marketingConsent:)`**. On the update, `false`
  records a withdrawal while omitting the field leaves consent untouched.
- **`Campaign::$family`, `$moment`, `$config` and `$version`** — `family` is what tells
  a points campaign from a customer-moment one.
- Ten error codes: `BRANCH_NOT_FOUND`, `BRANCH_INACTIVE`, `BRANCH_REQUIRED`,
  `CUSTOMER_ALREADY_LINKED`, `CUSTOMER_HAS_NO_EMAIL`, `LOYALTY_CARD_NOT_FOUND`,
  `CARD_SEND_THROTTLED`, `VOUCHER_NOT_FOUND`, `VOUCHER_EXPIRED`,
  `VOUCHER_ALREADY_USED`.

### Upgrading

Every new parameter is optional and appended, so existing calls compile unchanged. Two
things to check:

```php
// Was: always an int. Now null for a campaign family that has no multiplier.
$campaign->multiplier ?? 1;

// Was: always present. Now null under a branch filter.
$stats->loyalty?->pointsIssued;
```

## 0.1.1 — 2026-07-30

### Fixed

- Retry a non-JSON 5xx — a proxy or load balancer answering for a failing backend — and
  a token grant that fails transiently, rather than surfacing either as a dead end.

### Changed

- Document the base URL the way the API docs state it (`https://puntjes.app/api/v1`),
  accepting the bare host as equivalent.
- Mark the wallet-pass methods experimental, and throw rather than return an empty save
  URL from `googlePassUrl()`.

## 0.1.0 — 2026-07-30

Initial release: the framework-agnostic PSR-18 client, OAuth `client_credentials` with
cached tokens, conservative retries keyed on idempotency, typed exceptions and error
codes, lazy pagination, and the customers, transactions, wallets, rewards, redemptions,
products, campaigns and statistics resources.
