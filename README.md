# puntjes/php-sdk

PHP client for the [Puntjes](https://puntjes.app) loyalty API.

It works anywhere PHP 8.1 runs: a Laravel webshop, a WooCommerce plugin, a POS bridge,
a cron script. It does not force an HTTP client on you.

The client is generated from the Puntjes OpenAPI spec, so it has every call the API has.

- **Laravel**: install [`puntjes/laravel`](https://github.com/PuntjesApp/puntjes-laravel) on top for config, a facade and cache-backed tokens.
- **WordPress / WooCommerce**: use this package directly, plus the [notes below](#wordpress--woocommerce).
- **Coming from 1.x?** Read [`UPGRADING.md`](UPGRADING.md).

## Install

```bash
composer require puntjes/php-sdk:^2.0
```

The SDK talks PSR-18, so it uses the HTTP client your project already has (Guzzle,
Symfony HttpClient, and so on). If you have none, add one:

```bash
composer require guzzlehttp/guzzle
```

## Quick start

```php
use Puntjes\Puntjes;

$puntjes = Puntjes::make(getenv('PUNTJES_CLIENT_ID'), getenv('PUNTJES_CLIENT_SECRET'));

$product = $puntjes->products()->byExternalId('SKU-1001')->get()->wait()->getData();

echo $product->getName();
```

Get the credentials in **Puntjes, Settings, API clients**. The secret is shown once.

A call is a path and an HTTP method: `->customers()->byCustomer('42')->wallet()->get()`
is `GET /customers/42/wallet`. Every call returns a promise. End it with `->wait()`, then
read the `data` of the answer with `->getData()`.

A purchase at the till:

```php
use Puntjes\Generated\Models\CreateTransactionData;
use Puntjes\IdempotencyKey;

$body = new CreateTransactionData();
$body->setIdentifier($scannedCard);
$body->setTotalAmount(4200);                       // cents
$body->setIdempotencyKey('order-'.$order->id);     // or IdempotencyKey::new()

$transaction = $puntjes->transactions()->post($body)->wait()->getData();

echo $transaction->getPointsEarned();
```

A query value goes in a request configuration:

```php
use Puntjes\Generated\Customers\Lookup\LookupRequestBuilderGetRequestConfiguration as Lookup;

$customer = $puntjes->customers()->lookup()->get(new Lookup(
    queryParameters: Lookup::createQueryParameters(identifier: $scannedCard),
))->wait()->getData();

echo $customer->getWalletBalance();
```

Pass query values to `createQueryParameters()` by name, as above. The generated method
takes its arguments in alphabetical order, so a new query value in a later release moves
the others, and a call by position then sends the wrong value.

`Puntjes::make()` takes `baseUrl` (default `https://puntjes.app`; the API docs form
`https://puntjes.app/api/v1` works too), `tokenStore`, `httpClient`, `requestFactory`,
`streamFactory`, `maxRetries`, `defaultHeaders` and `sleeper`.

## Clearing a field

A setter with null does not clear a field: the client leaves a null value out of the
request body. To clear a field in a PATCH (`UpdateCustomerData`, `UpdateProductData`), put
the null in the additional data:

```php
use Puntjes\Generated\Models\UpdateCustomerData;

$body = new UpdateCustomerData();
$body->setAdditionalData(['phone' => null]);   // sends {"phone": null}
```

## Amounts are in cents

Every money value on this API is a whole number of cents. 42.00 euro is `4200`.

## Retries and idempotency

A retry of a request that already moved points is how an integration pays a customer
twice. So the SDK retries only what is safe.

| Call | Retried? | Why |
|---|---|---|
| Any `GET` | Yes | A read changes nothing |
| `PUT`, `PATCH`, `DELETE` | Yes | They name the record, so a repeat gives the same result |
| A `POST` with an `idempotency_key` in the body | Yes | The API answers a repeated key with the first answer |
| A `POST` without one | No | A repeat could create a second customer, send a second email or spend a code twice |

A retry happens only after a failure that a later try could survive: no connection, a 5xx
or a `429 RATE_LIMITED`. The wait doubles each time, and a `Retry-After` header wins.
`429 PLAN_LIMIT_EXCEEDED` is never retried: waiting does not clear it.

The SDK does not make an `idempotency_key` for you. Set one on every POST that moves points
(`transactions`, `redemptions`, `wallet/adjust`), and on `vouchers/{code}/verify` and
`products/{sku}/reward`, where a key also makes the call safe to retry.
`IdempotencyKey::new()` gives a new random key. A key from your own data, such as
`'order-'.$order->id`, is better: it is the same after a restart.

Keys belong to one shop. The same key with another customer, amount or reward answers
`IDEMPOTENCY_KEY_CONFLICT`, and nothing is written.

## Pages

A list answer holds one page. `Paginator::of()` walks all of them, and asks for the next
page only when your loop needs it:

```php
use Puntjes\Generated\Products\ProductsRequestBuilderGetRequestConfiguration as Products;
use Puntjes\Pagination\Paginator;

$all = Paginator::of(fn (int $page) => $puntjes->products()->get(new Products(
    queryParameters: Products::createQueryParameters(page: $page),
))->wait());

foreach ($all as $product) {
    echo $product->getExternalId();
}
```

`rewards()->get()` and a customer's `vouchers()->get()` are not paged. They answer in one call.

## Wallet passes

`WalletPass::apple($puntjes, $customerId)` returns the Apple pass (a `.pkpass` file) as a stream.
For Google, call `->walletPass()->get()` with the query `platform: google` and read
`->getData()->getSaveUrl()`.

## Errors

Everything the SDK throws extends `Puntjes\Exception\PuntjesException`.

```php
use Puntjes\Exception\{ApiException, NotFoundException, ValidationException};
use Puntjes\Generated\Errors\ErrorCode;

try {
    $puntjes->redemptions()->post($body)->wait();
} catch (NotFoundException $e) {
    // 404: no such customer or reward
} catch (ValidationException $e) {
    $e->errorsFor('reward_id');   // ['The reward id field is required.']
} catch (ApiException $e) {
    if ($e->errorCode() === ErrorCode::InsufficientBalance) {
        // ...
    }

    $e->status();      // 422
    $e->code();        // 'INSUFFICIENT_BALANCE'
    $e->requestId();   // quote this in a support ticket
}
```

| Exception | When |
|---|---|
| `AuthenticationException` | 401. The token is missing, unreadable, expired or revoked. The SDK already got a new token once, so the credentials are wrong or revoked. Or the client may not use client credentials |
| `ForbiddenException` | 403: the shop is pending, suspended or deactivated |
| `NotFoundException` | 404: no such record for this shop |
| `ConflictException` | 409: a duplicate identifier, external id or SKU |
| `ValidationException` | 422 `VALIDATION_ERROR`, with `errors()` |
| `RateLimitException` | 429 `RATE_LIMITED`, with `retryAfter()` |
| `PlanLimitExceededException` | 429 `PLAN_LIMIT_EXCEEDED`: the plan is used up |
| `ServerException` | 5xx |
| `ApiException` | Any other API error, such as 400 `INVALID_JSON` and the 422 refusals |
| `TransportException` | No HTTP answer at all, or a body that is not JSON |
| `ConfigurationException` | Bad settings, found before any request |

The API answers in JSON only. A success answer with another `Content-Type`, or with JSON
that does not parse, throws `TransportException`. An answer with no `Content-Type` is read
as JSON.

A 422 can be a bad field or a business refusal like `INSUFFICIENT_BALANCE`. Only the bad
field is a `ValidationException`, so catching it never hides a refusal you need to handle.

`ErrorCode` lists every code the API documents. For a code that is newer than your SDK,
`errorCode()` is null and `code()` still gives the raw string.

## Token storage

By default a token lives in memory for one PHP process. Under PHP-FPM that means one token
request per web request. It works, but a shared store is better:

```php
$puntjes = Puntjes::make($id, $secret, tokenStore: $yourStore);
```

`TokenStore` has three methods: `get`, `put` and `forget`. Laravel users get a cache-backed
store from `puntjes/laravel`. A WordPress one is below.

## Your own HTTP client

Timeouts, proxies and TLS belong to the HTTP client:

```php
$puntjes = Puntjes::make(
    $id,
    $secret,
    httpClient: new GuzzleHttp\Client([
        'timeout' => 10,
        'connect_timeout' => 5,
        'http_errors' => false,   // let the SDK turn error answers into exceptions
    ]),
);
```

## WordPress / WooCommerce

Two things matter when you ship this inside a plugin.

**1. Scope your dependencies.** A WordPress site runs all plugins in one PHP process. If two
plugins bundle different versions of one library, one of them breaks. Run
[PHP-Scoper](https://github.com/humbug/php-scoper) or
[Strauss](https://github.com/BrianHenryIE/strauss) over your vendor folder before you
distribute it. Scope every package this SDK installs. On PHP 8.4 with the newest versions
that is:

- `guzzlehttp/psr7`, `ralouphie/getallheaders`, `php-http/discovery`, `php-http/promise`
  and the `psr/*` interfaces (`psr/http-client`, `psr/http-factory`, `psr/http-message`,
  `psr/cache`, `psr/log`)
- the Kiota packages (`microsoft/kiota-abstractions`, `microsoft/kiota-serialization-json`,
  `microsoft/kiota-serialization-text`) and `stduritemplate/stduritemplate`
- what Kiota needs: `open-telemetry/api`, `open-telemetry/context`, `open-telemetry/sdk`,
  `open-telemetry/sem-conv`, `tbachert/spi`, `nyholm/psr7-server`, `composer/semver`,
  `doctrine/annotations`, `doctrine/lexer`, `ramsey/uuid`, `ramsey/collection` and
  `brick/math`
- the Symfony polyfills (`symfony/deprecation-contracts`, `symfony/polyfill-*`)

Run `composer show --no-dev` in your plugin to see the exact list for your PHP version.

**2. Cache tokens in a transient.** Without it, every page load asks for a new token:

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
        return 'puntjes_'.md5($key);   // transient keys are 172 characters at most
    }
}
```

Keep the credentials in `wp_options`, encrypted at rest where the host allows it, and never
log the secret.

## Rate limits

The limit belongs to the shop, and its size follows the shop's plan. Plans can differ, so
this README gives no number. Every API client and every server of one shop share the same
limit. A shop that may not use the API is limited per IP address instead.

When the API answers `429 RATE_LIMITED`, the SDK waits for the `Retry-After` time (or backs off when there is none) and tries
again, up to `maxRetries`, and then throws `RateLimitException`. It does this only for a call
that is safe to repeat (see [Retries and idempotency](#retries-and-idempotency)).

## Changing the secret without downtime

A shop can have more than one active API client, so no maintenance window is needed:

1. Create a new API client in the portal.
2. Deploy the new credentials.
3. Revoke the old client.

Tokens are cached per credential pair, so the two never mix.

## Development

```bash
composer install
composer test            # the Unit suite, no network
composer test:contract   # the Contract suite, needs a local Puntjes app
composer analyse         # PHPStan
composer format          # Pint
```

### Contract tests

The Unit suite checks the SDK against answers its authors wrote. The Contract suite calls
a real app, so a wrong guess about the wire cannot hide. It runs only against a local
app, because it creates customers, transactions and redemptions:

```bash
PUNTJES_BASE_URL=http://localhost \
PUNTJES_CLIENT_ID=... \
PUNTJES_CLIENT_SECRET=... \
composer test:contract
```

With the variables unset, or a base URL that is not local, the suite fails. A refusal is a
failure, never a skip, because a skipped suite is green with no call made.

The suite also checks every answer against `spec/api.json`. A field the spec marks as
always present fails the suite when the answer leaves it out, also when no test reads it.

### Checks on a pull request

Every pull request and every push to `main` runs PHPUnit (Unit suite), PHPStan and Pint in
three runs:

| Run | What it installs | What it proves |
|---|---|---|
| `PHP 8.1 · oldest allowed packages` | The oldest version of every package that `composer.json` allows | The lower bounds in `composer.json` are true |
| `PHP 8.1 · newest packages` | The newest versions that install on PHP 8.1 | The package works on its lowest PHP version today |
| `PHP 8.4 · newest packages` | The newest versions of everything | The package works on a current PHP |

The check named `CI` is green only when all three runs are green.

### Releasing

Nobody runs a release by hand. [release-please](https://github.com/googleapis/release-please)
reads the conventional commits on `main` (`feat:`, `fix:`, `feat!:`) and keeps one release
pull request open. That pull request holds the next version and its `CHANGELOG.md` entry.
It merges itself when CI is green. The merge tags the version, and Packagist reads the tag.

release-please chooses the version from the commit types since the last release. A `fix:`
raises the patch number, a `feat:` raises the minor number, and a `feat!:` or a
`BREAKING CHANGE:` line in the body raises the major number. Nobody sets the version by hand.

Write commit titles in the conventional form, because they become the changelog.

A new Puntjes API spec reaches this repository as a regenerate pull request. The
`Regenerate` workflow opens it on the branch `regenerate/<tag>` and closes the open ones for
older tags. A minor or patch one merges itself when CI is green. A major one asks
`@cedricdhooge96` for a review and waits. The new pull request is a major too when it
replaces an open major one, or when `spec/SOURCE` on `main` has a lower major version.
The workflow checks out without keeping a token, so its check for an existing branch
(`git ls-remote origin`) works only while this repository is public.

Three cases need a person:

- **The workflow fails.** It opens the issue "Regenerate failed", or comments on it when it
  is open. Read the run, fix the cause, and re-run it. The re-run picks up where the run
  stopped.
- **A regenerate pull request has red CI.** It stays open and does not merge. No issue
  tells you, so check the open `regenerate/*` pull requests after each Puntjes release.
- **A major regenerate pull request waits for its review.**

## Versions

2.x is the current line. 1.x gets security fixes for 6 months after 2.0.0 reaches Packagist,
on the `1.x` branch. Moving from 1.x is described in [`UPGRADING.md`](UPGRADING.md).

## Links

- Changelog: [`CHANGELOG.md`](CHANGELOG.md)
- Upgrading from 1.x: [`UPGRADING.md`](UPGRADING.md)
- API documentation: <https://docs.puntjes.app>
- OpenAPI spec: `{your Puntjes host}/docs/api.json`

## License

MIT.
