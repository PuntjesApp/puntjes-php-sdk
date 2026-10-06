# Changelog

Notable changes to `puntjes/php-sdk`. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this package follows
[semantic versioning](https://semver.org/). From 1.0.0 that promise is the ordinary one:
a breaking change waits for the next major, so `^1.0` is safe to pin and leave.

## 1.3.0 — 2026-10-06

### Added

- **A discount on one product.** Puntjes lets a discount reward, or a campaign discount
  gift, count on one product instead of the whole purchase (PuntjesApp/Puntjes#978).
  - `Redemption::productReference()` reads the item number from a redemption: the product a
    discount comes off, or the product to hand over for a free product. Null for a discount
    on the whole purchase.
  - `VoucherDiscount::$productReference` and `VoucherDiscount::isOnOneProduct()` read it from
    a verified voucher. The new property sits last with a default, so positional
    construction keeps working, and a Puntjes that does not send it reads as null.
  - A campaign's verbatim `config` may now hold `gift.discount.product_id`.
  For a discount on one product, pass `VoucherDiscount::appliedTo()` the amount it counts on:
  that product's price, or its line total if the till applies it to every unit. Puntjes leaves
  that choice to the till.

## 1.2.0 — 2026-10-05

### Added

- **Import-free loyalty rates** on `LoyaltyStatistics`, from `GET /statistics`. After a shop
  moves its customers over from another loyalty system, the customers bring their old points
  along. Those points never count as issued, but they do count in `pointsRedeemed` and
  `pointsExpired` when they are spent or expire, so `redemptionRate` and `breakageRate` can
  pass 1.0 for up to 90 days. Four new properties keep them apart:
  - `pointsRedeemedFromImport` and `pointsExpiredFromImport`, the imported part of each total;
  - `redemptionRateExcludingImport` and `breakageRateExcludingImport`, the rates with the
    imported points left out of both sides: the rates the Puntjes dashboard shows.
  Read the new rates first and fall back to the old ones. The old properties keep their values.
  The new properties come last, with a default, so code that builds the class by position keeps
  working; a Puntjes that does not send the fields yet reads as `0` and `null`.
- **`ErrorCode::InvalidJson`** (`INVALID_JSON`, 400), the answer a write endpoint gives when it
  cannot read the request body: the JSON is cut off, or it is not valid UTF-8. The API creates,
  changes and sends nothing. Before, such a body was read as empty, so `register()` could make an
  empty customer and `sendCard()` could send a card. It stays a plain `ApiException`, and the SDK
  never replays it, because the same body fails the same way. An empty body, `{}`, `[]` and
  `null` keep their answers. Older SDK versions already surface the code through
  `ApiException::code()`; this release names it on the enum and pins the mapping with tests.
- **`ErrorCode::CustomerEmailSuppressed`** (`CUSTOMER_EMAIL_SUPPRESSED`, 422), the
  answer both send-card operations give when earlier mail to the customer's address
  bounced or was marked as spam. The API refuses before it queues anything, so the
  per-customer cooldown is not spent. A till that catches it asks for an address that
  works; the vendor can also switch sending back on from the customer's page in the admin
  portal. Older SDK versions already surface the code through `ApiException::code()`;
  this release names it on the enum, documents it on `Customers::sendCard()` and
  `sendCardByExternalId()`, and pins the mapping with a test.
- **An extra amount on free-product rewards**, for a reward that costs points plus money
  ("500 points + € 2,00"). The till collects the money; Puntjes does not. The amount is in
  cents, and `0` means points only:
  - `RewardSummary::$paymentAmount` (`GET /rewards`) and `Reward::$paymentAmount`
    (`createReward()`), so a till or webshop can show the amount before the customer picks.
  - `Redemption::paymentAmount()`, the amount to collect for that redemption; `0` for a
    discount.
  - `CreateRewardFromProduct::$paymentAmount`, an optional last argument that
    `createReward()` sends as `payment_amount`.
  The new properties and the new argument come last, with a default, so code that builds
  these classes by position keeps working. An API that does not send `payment_amount` yet
  reads as `0`.

### Changed

- **Text the API cannot read now gets a clear answer, with no SDK code change.** A query value
  or form field that is not valid UTF-8 answers 422 `VALIDATION_ERROR` and names the field, so
  it arrives as a `ValidationException`. Before, it answered 500 `INTERNAL_ERROR`. A path with a
  NUL byte or invalid UTF-8 answers 404 `ROUTE_NOT_FOUND`, so it arrives as a `NotFoundException`.
  Before, a NUL byte cut the key short, and `delete('SKU-1%00')` deleted `SKU-1`. Tests now pin
  both mappings.
- **`Campaign::$config['scope']` can read `whole` or `whole_purchase`, and both mean the whole
  purchase.** The campaign builder stores `whole`; campaigns older than campaign families
  store `whole_purchase`. A `days_of_week` schedule from an older release can also store
  Sunday as `7` next to `0`. The SDK hands `config` and `recurrenceConfig` over as the API
  sends them, and a test now pins that, so no stricter model refuses a live campaign.
- **`Redemption::$typeSpecificData` is now a copy made at the moment of redemption.** If
  the vendor edits the reward afterwards, `find()`, `forCustomer()` and `verify()` still
  answer the values the customer redeemed: the extra amount and the product reference of a
  free product, and the discount value and type of a discount. Before, those calls read the
  reward as it was at the time of the call. The SDK code did not change for this; the
  docblocks now say it.

## 1.1.0 — 2026-09-30

### Added

- **`Redemptions::forCustomer(int $customerId, ?RedemptionStatus $status = null)`**, for
  `GET /customers/{customer}/redemptions`. It pages through one customer's redemptions,
  newest first, 15 per page. A till whose customer comes to collect a reward without the
  confirmation code looks the customer up by card, asks for `RedemptionStatus::Valid`, and
  verifies the one the customer picks. The status is the one each code has at the moment
  of the request: a code past its expiry reads `expired` and is left out of `Valid`, even
  before anyone looked it up. Each item has the same fields as `find()`. A customer of
  another vendor answers `CUSTOMER_NOT_FOUND`.

### Changed

- **`POST /transactions` now refuses a reused idempotency key that names another
  customer or another `totalAmount`.** The API used to echo the original transaction,
  so a till that reused a key by mistake was told "recorded" for a customer whose wallet
  had not moved. It now answers `422 IDEMPOTENCY_KEY_CONFLICT`, the same contract the
  adjust and redeem endpoints already had, and nothing is written. The SDK already
  maps that answer to an `ApiException` carrying the code; this release documents it on
  `Transactions::submit()` and `SubmitTransaction`, and pins the mapping with a test. No
  code change is needed on your side unless you relied on the echo.

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
