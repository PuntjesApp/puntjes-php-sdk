# puntjes/php-sdk

Framework-agnostic PHP client for the [Puntjes](https://puntjes.app) loyalty API.

Works anywhere PHP 8.1 runs — a Laravel webshop, a WooCommerce plugin, a POS bridge,
a cron script. No HTTP client is forced on you.

- **Laravel** → install [`puntjes/laravel`](https://github.com/TheGangOfFour/puntjes-laravel) on top for config, a facade and cache-backed tokens.
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

Keys are scoped per vendor. Reusing one for a different customer or reward returns
`IDEMPOTENCY_KEY_CONFLICT` rather than someone else's confirmation code.

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
| `ApiException` | Any other API error, including 422 domain refusals |
| `TransportException` | No HTTP response at all, or a non-JSON body |
| `ConfigurationException` | Bad settings — raised before any request |

> `422` covers both validation failures *and* domain refusals like
> `INSUFFICIENT_BALANCE` or `OUT_OF_STOCK`. Only the former is a `ValidationException`,
> so catching it never silently swallows a business outcome you needed to handle.

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
$puntjes->redemptions->verify('PNTJ-ABC123');

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
$puntjes->statistics->get(Period::ThirtyDays);
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

- API documentation — <https://docs.puntjes.app>
- OpenAPI spec — `{your Puntjes host}/docs/api.json`

## License

MIT.
