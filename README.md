# puntjes/php-sdk

Framework-agnostic PHP client for the [Puntjes](https://puntjes.app) loyalty API.

Works anywhere PHP 8.1 runs — a Laravel webshop, a WooCommerce plugin, a POS bridge,
a cron script. No HTTP client is forced on you.

- **Laravel** → install [`puntjes/laravel`](https://github.com/PuntjesApp/puntjes-laravel) on top for config, a facade and cache-backed tokens.
- **WordPress / WooCommerce** → use this package directly, plus the [notes below](#wordpress--woocommerce).

## Install

```bash
composer require puntjes/php-sdk
```

The SDK talks PSR-18, so it uses whatever HTTP client your project already has
(Guzzle, Symfony HttpClient, …). If you have none, add one:

```bash
composer require guzzlehttp/guzzle
```

## Quick start

```php
use Puntjes\Puntjes;
use Puntjes\Request\SubmitTransaction;

$puntjes = Puntjes::make(
    clientId:     getenv('PUNTJES_CLIENT_ID'),
    clientSecret: getenv('PUNTJES_CLIENT_SECRET'),
    baseUrl:      'https://puntjes.app/api/v1',
);

// A customer scans their card at the till.
$customer = $puntjes->customers->lookup(identifier: $scannedCard);

echo $customer->fullName();      // "Jan Everaert"
echo $customer->walletBalance;   // 320  (lookup folds the balance in)

// Record the purchase. Amounts are in cents.
$transaction = $puntjes->transactions->submit(new SubmitTransaction(
    identifier:     $scannedCard,
    totalAmount:    4200,
    idempotencyKey: 'order-'.$order->id,
));

echo $transaction->pointsEarned; // 42
```

Credentials come from **Puntjes → Settings → API clients**. The secret is shown once.

Authentication is handled for you: a `client_credentials` token is fetched on the
first call, cached until it expires, and silently re-fetched once if the API ever
rejects it.

### Base URL

Pass the base URL exactly as the [API docs](https://docs.puntjes.app) state it —
`https://puntjes.app/api/v1`. The bare host works too; both are accepted and
equivalent:

```php
baseUrl: 'https://puntjes.app/api/v1'   // as documented
baseUrl: 'https://puntjes.app'          // same thing
```

Internally the SDK keeps only the host, because the two endpoints it talks to do not
share a prefix: the API is under `/api/v1`, but the OAuth token endpoint is at
`/oauth/token`, off the root.

## Amounts are in cents

Every monetary value on this API — `totalAmount`, `unitPrice`, `priceCents`,
`revenueCents` — is an integer number of cents. €42.00 is `4200`.

The single exception is `Campaign::$minTransactionAmount`, which the API serialises
in euros. It is documented on the model.

## Retries and idempotency

Retrying a request that already moved points is how integrations double-credit
customers, so the SDK is deliberately conservative about what it replays.

| Call | Auto-retried? | Why |
|---|---|---|
| Any `GET` | ✅ | Reads have no effect |
| `PUT` / `PATCH` / `DELETE` | ✅ | All keyed by external id; they converge |
| `POST /transactions` | ✅ | Carries an `idempotency_key` |
| `POST /redemptions` | ✅ | Carries an `idempotency_key` |
| `POST …/wallet/adjust` | ✅ | Carries an `idempotency_key` |
| `POST /customers` | ❌ | A replay would create a second customer |
| `POST /products`, `/products/batch`, `/products/{sku}/reward` | ❌ | No replay protection |
| `POST /redemptions/{code}/verify` | ❌ | A replay would answer `CODE_ALREADY_USED` |
| `POST /vouchers/{code}/verify` | ❌ | Verifying a bon *spends* it; a replay answers `VOUCHER_ALREADY_USED` |
| `POST …/send-card` | ❌ | A replay emails the customer a second time |
| `POST /customers/link-external-id` | ❌ | No idempotency key — though re-sending the *same* external id is safe if you retry yourself |

Retries fire only on failures a later attempt could survive — connection errors, 5xx,
and `429 RATE_LIMITED` — with exponential backoff, honouring `Retry-After`.
`429 PLAN_LIMIT_EXCEEDED` is excluded: it shares the status code but is a billing
stop that waiting cannot clear.

**Idempotency keys** are generated automatically when you do not supply one, and the
same key is reused across the SDK's own retries. That protection ends with the object,
though — if *your* code catches a failure and rebuilds the request, derive the key
from something stable in your system:

```php
new SubmitTransaction(
    identifier:     $card,
    totalAmount:    4200,
    idempotencyKey: 'order-'.$order->id,   // survives a process restart
);
```

Keys are scoped per vendor. Reusing one for a different customer, a different
`totalAmount` or a different reward returns `IDEMPOTENCY_KEY_CONFLICT` rather than
someone else's transaction or confirmation code, and nothing is written.

## Branches

A vendor can run several shops, and the API can record which one a purchase, a
redemption or a bon happened at. A branch is addressed by the **key the vendor chose**
in the portal — never by a Puntjes id — so nothing here asks your system to store a
primary key it would then have to keep in step.

### Naming a branch on a write

```php
$puntjes->transactions->submit(new SubmitTransaction(
    identifier:  $scannedCard,
    totalAmount: 4200,
    branch:      'centrum',
));

$puntjes->redemptions->create(new CreateRedemption('CARD-1', rewardId: 3, branch: 'centrum'));
$puntjes->vouchers->verify('BON-ABC12345', branch: 'centrum');
```

The API resolves it in one order: **the payload, then the branch your API credential
defaults to, then no branch at all.** A till that only ever serves one shop is better
configured once — set the default branch on its credential in *Settings → API clients* —
than made to send `branch:` on every call.

Two ways it can be wrong, kept deliberately distinct so you can tell them apart:

| Code | Meaning |
|---|---|
| `BRANCH_NOT_FOUND` | The key names no branch this vendor has. You typed it wrong. |
| `BRANCH_INACTIVE` | The vendor closed that shop. Only `POST /transactions` raises it. |
| `BRANCH_REQUIRED` | The reward or bon is limited to particular branches, and this is not one. |

All three are 422 and nothing is recorded.

### Reading a branch back

```php
$transaction->branch?->name;       // "Centrum", or null for the Unassigned bucket
$transaction->branch?->externalId; // "centrum" — the key to send back
$transaction->branch?->type;       // BranchType::Physical
```

Rewards and campaigns carry the branches they are limited to. **`null` and `[]` mean
opposite things and are never collapsed:**

```php
$reward->branches === null   // redeemable anywhere
$reward->branches === []     // scoped, but every shop it named has since been deleted
$reward->isRedeemableEverywhere();
$campaign->runsEverywhere();
```

### Rates after an import

```php
$loyalty = $puntjes->statistics->get(Period::ThirtyDays)->loyalty;

$rate = $loyalty?->redemptionRateExcludingImport ?? $loyalty?->redemptionRate;
```

Points a customer brought along from another loyalty system never count as issued, but they
do count when they are spent or expire, so `redemptionRate` and `breakageRate` can pass 1.0
after an import. The `…ExcludingImport` rates leave those points out of both sides, as the
Puntjes dashboard does. They are null when nothing was issued, and from a Puntjes that does
not send them yet, so the fallback above covers both.

### Filtering a report

```php
use Puntjes\Model\Branch;

$puntjes->statistics->get(Period::ThirtyDays, branch: 'centrum');
$puntjes->statistics->get(Period::ThirtyDays, branch: Branch::UNASSIGNED);  // 'none'
$puntjes->campaigns->list(branch: 'centrum');
```

There are three states here, not two: **omitting** `branch` covers the whole vendor,
`Branch::UNASSIGNED` covers only what was recorded against no branch, and a key covers
that one shop. A mistyped key is refused with `BRANCH_NOT_FOUND` rather than answered
vendor-wide — a report that silently widens under a branch label is the one mistake
nobody catches by reading it.

`Branch::UNASSIGNED` is a **filter word only**. On a write it is an unknown key, because
a purchase has to have happened somewhere.

> Under a branch filter, `$stats->loyalty` is **null**. Points liability is a wallet
> snapshot and breakage is a ratio whose halves come from different populations —
> neither narrows to one shop, so the API omits the block rather than printing a
> vendor-wide number beside branch-filtered sales. Check for null before reading it.

## Errors

Everything the SDK raises extends `Puntjes\Exception\PuntjesException`.

```php
use Puntjes\Enum\ErrorCode;
use Puntjes\Exception\{ApiException, NotFoundException, ValidationException};

try {
    $puntjes->redemptions->create($redemption);
} catch (NotFoundException $e) {
    // 404 — unknown customer or reward
} catch (ValidationException $e) {
    $e->errorsFor('reward_id');   // ['The reward id field is required.']
} catch (ApiException $e) {
    if ($e->is(ErrorCode::InsufficientBalance)) { … }

    $e->status();      // 422
    $e->code();        // 'INSUFFICIENT_BALANCE'
    $e->requestId();   // quote this in a support ticket
}
```

| Exception | When |
|---|---|
| `AuthenticationException` | 401 — bad, revoked or unlinked credentials |
| `ForbiddenException` | 403 — vendor pending, suspended or deactivated |
| `NotFoundException` | 404 — no such record *for this vendor* |
| `ConflictException` | 409 — duplicate identifier, external id or SKU |
| `ValidationException` | 422 `VALIDATION_ERROR`, with `errors()` |
| `RateLimitException` | 429 `RATE_LIMITED`, with `retryAfter()` |
| `PlanLimitExceededException` | 429 `PLAN_LIMIT_EXCEEDED` — upgrade the plan |
| `ServerException` | 5xx |
| `ApiException` | Any other API error, including 400 `INVALID_JSON` and 422 domain refusals |
| `TransportException` | No HTTP response at all, or a non-JSON body |
| `ConfigurationException` | Bad settings — raised before any request |

> `422` covers both validation failures *and* domain refusals like
> `INSUFFICIENT_BALANCE` or `OUT_OF_STOCK`. Only the former is a `ValidationException`,
> so catching it never silently swallows a business outcome you needed to handle.

### Unreadable requests

Some requests reach the API in a shape it cannot read. The answer is always an error, and
the API changes nothing:

| What was sent | Answer | Exception |
|---|---|---|
| A JSON body that is cut off, or is not valid UTF-8 | 400 `INVALID_JSON` | `ApiException` |
| A query value or form field that is not valid UTF-8 | 422 `VALIDATION_ERROR`, with the field named | `ValidationException` |
| A path with a NUL byte or text that is not valid UTF-8 | 404 `ROUTE_NOT_FOUND` | `NotFoundException` |

Sending the same request again gives the same answer, so the SDK never replays these.
Fix the text in your own code first. For `INVALID_JSON`, check `$e->is(ErrorCode::InvalidJson)`.
The SDK writes JSON with `json_encode` and stops on bad UTF-8 before it sends, so you should rarely see this code.

`ErrorCode` is an enum of every documented code. Unknown codes — a newer API than
your SDK — leave `errorCode()` null while `code()` still returns the raw string, so a
new server-side code can never break an older client.

## Pagination

List endpoints return a lazy `Paginator`. Iterating it walks every page on demand.

```php
foreach ($puntjes->products->list() as $product) {   // fetches pages as needed
    echo $product->externalId;
}

$page = $puntjes->products->list()->firstPage();     // just the first page
$page->meta->total;
$page->hasMorePages();

$all = $puntjes->campaigns->list()->all();           // everything, eagerly
```

`rewards->list()` is not paginated — the catalogue comes back in one call.

## Endpoints

```php
// Customers
$puntjes->customers->lookup(identifier: 'CARD-1');       // or externalId:
$puntjes->customers->findByIdentifier('CARD-1');         // null instead of throwing
$puntjes->customers->findByExternalId('PNU-1');
$puntjes->customers->register(new CreateCustomer(...));
$puntjes->customers->find(42);
$puntjes->customers->updateByExternalId('PNU-1', new UpdateCustomer(email: 'new@example.com'));
$puntjes->customers->linkExternalId('CARD-1', 'PNU-1');   // backfill a legacy customer
$puntjes->customers->sendCard(42);                        // email them their loyalty card
$puntjes->customers->sendCardByExternalId('PNU-1');

// Transactions
$puntjes->transactions->submit(new SubmitTransaction(...));
$puntjes->transactions->forCustomer(42, new DateRangeFilters(dateFrom: '2026-07-01'));

// Wallets
$puntjes->wallets->show(42);
$puntjes->wallets->ledger(42, new DateRangeFilters(type: 'earn'));
$puntjes->wallets->adjust(42, AdjustWallet::credit(100, 'Goodwill'));
$puntjes->wallets->applePass(42);        // raw .pkpass bytes — EXPERIMENTAL, see below
$puntjes->wallets->googlePassUrl(42);    // save URL to redirect to — EXPERIMENTAL, see below

// Rewards & redemptions
$puntjes->rewards->list();
$puntjes->rewards->list(affordableFor: 'CARD-1');
$puntjes->redemptions->create(new CreateRedemption('CARD-1', rewardId: 3));
$puntjes->redemptions->find('PNTJ-ABC123');
$puntjes->redemptions->forCustomer(42, RedemptionStatus::Valid);   // rewards still to collect, no code needed
$puntjes->redemptions->verify('PNTJ-ABC123');

// Campaign bonnen — the vouchers a campaign gives away. Verifying SPENDS one.
$puntjes->vouchers->verify('BON-ABC12345');

// Products — keyed on YOUR SKU, never on a Puntjes id
$puntjes->products->list(new ProductFilters(category: 'Bakery'));
$puntjes->products->create(new CreateProduct(externalId: 'SKU-1', name: 'Brood'));
$puntjes->products->find('SKU-1');
$puntjes->products->findOrNull('SKU-1');
$puntjes->products->upsert('SKU-1', new UpsertProduct(name: 'Brood'));   // sync primitive
$puntjes->products->update('SKU-1', new UpdateProduct(priceCents: 275)); // partial
$puntjes->products->delete('SKU-1');
$puntjes->products->batchUpsert(['SKU-1' => $a, 'SKU-2' => $b]);         // max 100
$puntjes->products->createReward('SKU-1', new CreateRewardFromProduct(pointCost: 200));

// Campaigns, statistics, vendor
$puntjes->campaigns->list();
$puntjes->campaigns->list(branch: 'centrum');
$puntjes->statistics->get(Period::ThirtyDays);
$puntjes->statistics->get(Period::ThirtyDays, branch: 'centrum');
$puntjes->me();
$puntjes->ping();   // unauthenticated health check
```

### Wallet passes (experimental)

`applePass()` and `googlePassUrl()` target `GET /customers/{id}/wallet-pass`, a
feature that is **not yet fully integrated on the Puntjes side**. Treat both as
experimental: verify them against your target environment before shipping, and give
your HTTP client a request timeout — an instance whose pass integration is incomplete
can hang rather than fail. `googlePassUrl()` throws a `TransportException` when the
response carries no usable save URL, so you can never end up redirecting a customer
to an empty string.

### Catalogue sync

`upsert()` is the primitive to build on: pushing the same row repeatedly converges
on one product, so a sync job is safe to re-run.

```php
$result = $puntjes->products->batchUpsert($chunk);   // up to 100 at a time
```

`batchUpsert` **never throws for a bad row** — each item is processed independently
and the call answers 200 even when every item failed. Always inspect the result:

```php
if ($result->hasFailures()) {
    foreach ($result->failures() as $failure) {
        $log->warning('Product rejected', [
            'sku' => $failure->externalId,
            'errors' => $failure->errors,
        ]);
    }
}
```

Note that `upsert()` (PUT) **replaces** the whole product — omitted nullable fields
are cleared. That is what makes it converge. Use `update()` (PATCH) to change one
field and leave the rest alone.

### Omitted vs null on partial updates

PATCH requests distinguish "leave this alone" from "clear this", so the defaults are
`Undefined`, not `null`:

```php
new UpdateCustomer(email: 'new@example.com')  // changes only the email
new UpdateCustomer(phone: null)               // clears the phone number
```

### Registering a customer, and what an identifier can be

Send no identifiers and Puntjes issues the loyalty card itself. That is the common case,
and it is the shortest correct call:

```php
$customer = $puntjes->customers->register(new CreateCustomer(firstName: 'Jan'));
$customer->loyaltyCardCode;   // "7KQ4M2XP", 8 chars of A-Z0-9
```

Pass a loyalty card only when the customer already holds a printed one. The value must be
a card your vendor has actually printed and not yet handed to anyone: the API matches it
against its own card stock, case-insensitively, and refuses anything else with a 422
`LOYALTY_CARD_NOT_FOUND`, or a 409 `IDENTIFIER_DUPLICATE` if that card already belongs to
somebody. You cannot invent the value.

```php
new CreateCustomer(identifiers: [CreateIdentifier::loyaltyCard('7KQ4M2XP')]);
new CreateCustomer(identifiers: [CreateIdentifier::email('jan@example.com')]);
```

Registration accepts those two types and nothing else. The scan technologies an earlier
API supported (`card`, `qr`, `nfc`, `barcode`) were removed, with no alias window, so
sending one is a 422 rather than a translation.

`IdentifierType::Phone` still exists because the API still returns it on customers
migrated before the change. It cannot be registered.

### Customer consent and the loyalty card code

Registration returns the customer's card code at the top level — persist it as their QR
value rather than digging it back out of the identifier list:

```php
$customer = $puntjes->customers->register($new);
$customer->loyaltyCardCode;   // "7KQ4M2XP", 8 chars of A-Z0-9
```

Marketing consent comes back decided, with the provenance behind it, so nothing has to
re-implement the grant-versus-withdrawal rule:

```php
$customer->hasMarketingConsent();                    // true
$customer->marketingConsent->grantedAt;              // "2026-03-03T09:00:00+00:00"
$customer->marketingConsent->grantedSource;          // "webshop"

new CreateCustomer(identifiers: [...], marketingConsent: true);
new UpdateCustomer(marketingConsent: false);         // records a withdrawal
new UpdateCustomer();                                // leaves consent exactly as it was
```

Only send `true` when the customer actually opted in on your side — this is the record
the vendor relies on to prove consent.

### Not every campaign multiplies points

`Campaign::$family` says what kind a campaign is. A `purchase` campaign carries a
multiplier and a schedule; a `customer_moment` one (a birthday gift, say) carries
neither and sends null for `multiplier`, `recurrenceType` and `recurrenceConfig`.
Branch on `family` before reading any of the three.

### A reward can cost points plus an amount

A free-product reward can ask for money on top of the points, for example "500 points +
€ 2,00". Puntjes does not take that money; the till collects it. The amount is in cents,
and `0` means points only.

```php
$rewards[0]->paymentAmount;       // 200: show it next to the points before the customer picks
$redemption->paymentAmount();     // 200: collect this at the till; always 0 for a discount
$redemption->productReference();  // 'STEAK-01': the item number of the product to hand over
$puntjes->products->createReward('SKU-1', new CreateRewardFromProduct(pointCost: 500, paymentAmount: 200));
```

A redemption keeps a copy of what it was worth when it was made. If the vendor changes the
reward later, `find()`, `forCustomer()` and `verify()` still answer the old amount, so
collect what the redemption says, not what the catalogue says today.

### A discount can be on one product

A discount reward or a campaign discount gift can count on one product instead of the
whole purchase, for example "20% off the kernthermometer". The till gets that product's
item number. With no item number, the discount counts on the whole purchase, as before.

```php
$redemption->productReference();                 // 'KT-10234', or null for the whole purchase
$voucher = $puntjes->vouchers->verify('BON-ABC12345');
$voucher->discount?->isOnOneProduct();           // true
$voucher->discount?->productReference;           // 'KT-10234'
$voucher->discount?->appliedTo($priceCents);     // the amount the discount counts on, see below
```

Pass `appliedTo()` the amount the discount counts on: that product's price, or its line total
if your till applies it to every unit. Puntjes leaves that choice to the till. A fixed amount
can be more than the product's price; `appliedTo()` never takes off more than you pass. A campaign's `config['gift']['discount']['product_id']` names the
product of a discount gift, as `GET /products` returns its `id`.

## Token storage

By default tokens live in memory for one PHP process, which under PHP-FPM means one
token grant per web request. Correct, but wasteful — supply a shared store:

```php
$puntjes = Puntjes::make(..., tokenStore: $yourStore);
```

`TokenStore` has three methods (`get`, `put`, `forget`). Laravel users get a
cache-backed implementation from `puntjes/laravel`; a WordPress one is below.

## Bringing your own HTTP client

Timeouts, proxies, TLS and instrumentation belong to the HTTP client, not to this SDK:

```php
$puntjes = Puntjes::make(
    ...,
    httpClient: new GuzzleHttp\Client([
        'timeout' => 10,
        'connect_timeout' => 5,
        'http_errors' => false,   // let the SDK map error responses
    ]),
);
```

## WordPress / WooCommerce

Two things matter when shipping this inside a plugin.

**1. Scope your dependencies.** A WordPress site runs every plugin in one PHP process.
If two plugins bundle different versions of the same library, one of them breaks. Run
[PHP-Scoper](https://github.com/humbug/php-scoper) or
[Strauss](https://github.com/BrianHenryIE/strauss) over your vendor directory before
distributing (Mozart is abandoned; Strauss is its successor). This SDK keeps its
dependency list to PSR interfaces plus discovery precisely to make that cheap.

**2. Cache tokens in a transient.** Otherwise every page load grants a new token:

```php
use Puntjes\Auth\{AccessToken, TokenStore};

final class TransientTokenStore implements TokenStore
{
    public function get(string $key): ?AccessToken
    {
        return AccessToken::fromArray(get_transient($this->key($key)) ?: null);
    }

    public function put(string $key, AccessToken $token, int $ttl): void
    {
        set_transient($this->key($key), $token->toArray(), max(1, $ttl));
    }

    public function forget(string $key): void
    {
        delete_transient($this->key($key));
    }

    private function key(string $key): string
    {
        // Transient keys are capped at 172 characters.
        return 'puntjes_'.md5($key);
    }
}
```

Store credentials in `wp_options`, encrypted at rest where the host allows it, and
never log the secret.

## Rate limits

Limits are per OAuth client (not per IP) and derived from the vendor's plan — 60
requests/minute on the free tier. Several servers sharing one client share one budget.
Exceeding it raises `RateLimitException`; the SDK already backs off and retries within
`maxRetries`.

## Secret rotation, without downtime

A vendor may hold up to 10 active API clients, so rotation needs no maintenance window:

1. Create a new API client in the portal.
2. Deploy the new credentials.
3. Revoke the old client.

Tokens are cached per credential pair, so the two never collide mid-rotation.

## Escape hatch

For an endpoint this SDK does not model yet, or a field added after this version
shipped — authentication, retries and error mapping still apply:

```php
$response = $puntjes->request('GET', '/some/new/endpoint', ['page' => 2]);
$response->dataArray();
```

## Development

```bash
composer install
composer test        # unit tests, no network
composer analyse     # PHPStan level 6
composer format      # Pint
```

### Contract tests

The unit suite proves the SDK behaves correctly against fixtures the SDK's own author
wrote — which is exactly the blind spot that lets a wrong assumption about the wire
format ship. The contract suite closes it by running against a real instance:

```bash
PUNTJES_BASE_URL=http://localhost \
PUNTJES_CLIENT_ID=… \
PUNTJES_CLIENT_SECRET=… \
vendor/bin/phpunit --testsuite Contract
```

It is skipped when those variables are unset. Everything it writes is a product with
a run-unique SKU, deleted afterwards whether or not the tests passed. It creates no
customer, transaction or points data — the API cannot delete those, and a test has no
business leaving balances behind.

## Links

- Changelog — [`CHANGELOG.md`](CHANGELOG.md)
- API documentation — <https://docs.puntjes.app>
- OpenAPI spec — `{your Puntjes host}/docs/api.json`

## License

MIT.
