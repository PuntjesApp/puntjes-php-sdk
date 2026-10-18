# Upgrading from 1.x to 2.0

2.0 is a new core. The client is now generated from the Puntjes OpenAPI spec, so it
always has the same calls and fields as the API. The token, retry and error rules are
the same as in 1.x, and the exception names are the same.

This guide shows what changed and how to write each 1.x call in 2.0.

## What changed

- **The client is generated.** `Puntjes::make()` returns a `Puntjes\Generated\PuntjesClient`.
  You walk the URL with methods (`->customers()->byCustomer('42')->wallet()`), then call
  the HTTP method (`->get()`). A type hint on `Puntjes\Puntjes` (a property, an argument,
  a container binding) becomes `Puntjes\Generated\PuntjesClient`.
- **Pass query values by name.** `createQueryParameters()` takes its arguments in
  alphabetical order. A new query value in a later release moves the others, so write
  `createQueryParameters(identifier: $card)`, never a call by position.
- **Every call returns a promise.** End it with `->wait()`. The answer has a `getData()`
  method for the `data` part of the JSON.
- **Models are generated.** `Puntjes\Model\*`, `Puntjes\Request\*` and `Puntjes\Enum\*` are
  gone. Request bodies are `Puntjes\Generated\Models\*Data` classes that you fill with
  setters. Answers have getters.
- **The exceptions keep their names.** `Puntjes\Exception\*` works as before. `errorCode()`
  now returns `Puntjes\Generated\Errors\ErrorCode` (it was `Puntjes\Enum\ErrorCode`).
  It is still null for a code this SDK does not know, and `code()` still gives the raw string.
- **Helpers.** `Puntjes\IdempotencyKey::new()`, `Puntjes\Pagination\Paginator::of()` and
  `Puntjes\WalletPass::apple()` cover the three things the generated client does not.

## Five behaviours to know

1. **The SDK no longer makes an `idempotency_key` for you.** In 1.x the resources added one
   inside the SDK. In 2.0 you pass one: `$body->setIdempotencyKey(IdempotencyKey::new())`.
   A key built from your own data (for example `'order-'.$order->id`) is better, because it
   survives a restart. A POST that carries a key is retried on a connection error, a 5xx or
   a 429. A POST without a key is never retried.
2. **A success answer without the `data` wrapper reads as empty data.** In 1.x this threw.
   In 2.0 `getData()` returns null. Check for null if you need the value.
3. **`GET /health` needs no credentials, and none are sent.** The SDK sends no token for an
   operation the spec marks as public.
4. **Iterate pages with `Paginator::of()`.** The `Paginator` class of 1.x (with `firstPage()`
   and `all()`) is gone. See the example below.
5. **A setter with null does not clear a field.** The generated models leave a null value
   out of the request body, so `$body->setPhone(null)` sends nothing for `phone`. In 1.x,
   `new UpdateCustomer(phone: null)` cleared the phone number. To clear a field in a PATCH of
   `UpdateCustomerData` or `UpdateProductData`, put the null in the additional data:
   `$body->setAdditionalData(['phone' => null])` sends `"phone": null`. Check this in code
   that removes personal data on request, because there a field that stays filled is a
   GDPR problem.

## Setting up

`Puntjes::make()` takes the same arguments as in 1.x, with two differences. `baseUrl` is
now optional and defaults to `https://puntjes.app`. There is a new last argument, `sleeper`:
a callable that receives the seconds to wait between retries, so tests do not wait. Both
forms of the base URL still work (`https://puntjes.app` and `https://puntjes.app/api/v1`).

`Puntjes::fromConfig()` is gone. Call `make()`.

In a container, resolve `PuntjesClient`, not `Puntjes`. `Puntjes\Puntjes` only builds clients, so a
container asked for it fails at once.

## Every 1.x call, in 2.0

`$c` is the client from `Puntjes::make()`. Each 2.0 call ends with `->wait()`, and the
answer is read with `->getData()` unless the table says otherwise. Query values go in a
request configuration, for example
`new LookupRequestBuilderGetRequestConfiguration(queryParameters: LookupRequestBuilderGetRequestConfiguration::createQueryParameters(identifier: $card))`.

### On the client

| 1.x | 2.0 |
|---|---|
| `$puntjes->me()` | `$c->me()->get()` |
| `$puntjes->ping()` | `$c->health()->get()` (no credentials; an error answer throws) |
| `$puntjes->request($method, $path)` | Removed. Use the generated call for the endpoint |
| `$puntjes->config()` | Removed |
| `Puntjes::fromConfig()` | Removed. Use `Puntjes::make()` |

### Customers

| 1.x | 2.0 |
|---|---|
| `customers->lookup(identifier:, externalId:)` | `$c->customers()->lookup()->get($config)`, query `identifier` or `external_id` |
| `customers->findByIdentifier($id)` | The same lookup. Catch `NotFoundException` yourself, because 1.x returned null |
| `customers->findByExternalId($id)` | The same lookup with `external_id`. Catch `NotFoundException` |
| `customers->register(CreateCustomer)` | `$c->customers()->post(CreateCustomerData)` |
| `customers->find($customerId)` | `$c->customers()->byCustomer((string) $customerId)->get()` |
| `customers->updateByExternalId($ext, UpdateCustomer)` | `$c->customers()->byExternalId()->byExternalId($ext)->patch(UpdateCustomerData)` |
| `customers->linkExternalId($identifier, $ext)` | `$c->customers()->linkExternalId()->post(LinkExternalIdData)` |
| `customers->sendCard($customerId, $channel)` | `$c->customers()->byCustomer((string) $customerId)->sendCard()->post(SendLoyaltyCardRequestData)` |
| `customers->sendCardByExternalId($ext, $channel)` | `$c->customers()->byExternalId()->byExternalId($ext)->sendCard()->post(SendLoyaltyCardRequestData)` |

New in 2.0: `->byExternalId($ext)->put(UpdateCustomerData)` replaces a customer by external id.

### Products

| 1.x | 2.0 |
|---|---|
| `products->list($filters)` | `Paginator::of(fn ($page) => $c->products()->get($config with page: $page)->wait())`. Filters are the query `category`, `search`, `status`, `per_page` |
| `products->create(CreateProduct)` | `$c->products()->post(CreateProductData)` |
| `products->find($ext)` | `$c->products()->byExternalId($ext)->get()` |
| `products->findOrNull($ext)` | The same call. Catch `NotFoundException` |
| `products->upsert($ext, UpsertProduct)` | `$c->products()->byExternalId($ext)->put(UpsertProductData)`. It returns the product only. 1.x also said whether it was created |
| `products->update($ext, UpdateProduct)` | `$c->products()->byExternalId($ext)->patch(UpdateProductData)` |
| `products->delete($ext)` | `$c->products()->byExternalId($ext)->delete()` |
| `products->batchUpsert($products)` | `$c->products()->batch()->post(BatchPostRequestBody)`, with `setProducts([...CreateProductData])` |
| `products->createReward($ext, CreateRewardFromProduct)` | `$c->products()->byExternalId($ext)->reward()->post(CreateRewardFromProductData)`. Set an idempotency key |

### Rewards and redemptions

| 1.x | 2.0 |
|---|---|
| `rewards->list(affordableFor:)` | `$c->rewards()->get($config)`, query `identifier` and `affordable: true` |
| `rewards->list(countRedemptionsFor:)` | The same call, query `identifier` only |
| `redemptions->create(CreateRedemption)` | `$c->redemptions()->post(CreateRedemptionData)`. Set an idempotency key |
| `redemptions->find($code)` | `$c->redemptions()->byCode($code)->get()` |
| `redemptions->forCustomer($id, $status)` | `Paginator::of(...)` over `$c->customers()->byCustomer((string) $id)->redemptions()->get($config)`, query `status` |
| `redemptions->verify($code)` | `$c->redemptions()->byCode($code)->verify()->post(VerifyRedemptionData)`, with `setBranch()` and `setIdempotencyKey()` |

### Transactions, wallets, vouchers

| 1.x | 2.0 |
|---|---|
| `transactions->submit(SubmitTransaction)` | `$c->transactions()->post(CreateTransactionData)`. Set an idempotency key |
| `transactions->forCustomer($id, $filters)` | `Paginator::of(...)` over `$c->customers()->byCustomer((string) $id)->transactions()->get($config)`, query `date_from`, `date_to` |
| `wallets->show($id)` | `$c->customers()->byCustomer((string) $id)->wallet()->get()` |
| `wallets->ledger($id, $filters)` | `Paginator::of(...)` over `$c->customers()->byCustomer((string) $id)->ledger()->get($config)`, query `date_from`, `date_to`, `type` |
| `wallets->adjust($id, AdjustWallet)` | `$c->customers()->byCustomer((string) $id)->wallet()->adjust()->post(AdjustWalletApiData)`. Set an idempotency key |
| `wallets->applePass($id)` | `WalletPass::apple($c, $id)`. It returns the pkpass bytes as a stream |
| `wallets->googlePassUrl($id)` | `$c->customers()->byCustomer((string) $id)->walletPass()->get($config)`, query `platform: google`, then `->getData()->getSaveUrl()` |
| `vouchers->forCustomer($id)` | `$c->customers()->byCustomer((string) $id)->vouchers()->get()` |
| `vouchers->find($code)` | `$c->vouchers()->byCode($code)->get()` |
| `vouchers->verify($code, $branch, $key)` | `$c->vouchers()->byCode($code)->verify()->post(VerifyCampaignVoucherData)`, with `setBranch()` and `setIdempotencyKey()` |

### Campaigns and statistics

| 1.x | 2.0 |
|---|---|
| `campaigns->list($branch)` | `Paginator::of(...)` over `$c->campaigns()->get($config)`, query `branch` |
| `statistics->get($period, $topProductsLimit, $branch)` | `$c->statistics()->get($config)`, query `period`, `top_products_limit`, `branch`. The 1.x defaults (30 days, 10 products) are now the API's defaults |

The helper methods of the 1.x models (`Customer::fullName()`, `RewardSummary::redemptionsLeft()`,
`OpenVoucher::isSpendableAt()` and the like) are gone. The generated models only hold the fields
of the API.

## Pages

```php
use Puntjes\Generated\Products\ProductsRequestBuilderGetRequestConfiguration as Config;
use Puntjes\Pagination\Paginator;

$products = Paginator::of(fn (int $page) => $puntjes->products()->get(new Config(
    queryParameters: Config::createQueryParameters(category: 'coffee', page: $page),
))->wait());

foreach ($products as $product) {
    echo $product->getExternalId();
}
```

`Paginator::of()` is lazy. It asks for the next page only when the loop needs it, and it
stops after the last page.

## Errors

```php
// 1.x
use Puntjes\Enum\ErrorCode;

// 2.0
use Puntjes\Generated\Errors\ErrorCode;
```

Nothing else changes: `ApiException::status()`, `code()`, `requestId()`, `is()`,
`ValidationException::errorsFor()` and `RateLimitException::retryAfter()` work as in 1.x.

## New packages

`composer require puntjes/php-sdk:^2.0` also installs the Kiota packages and what they need,
such as `open-telemetry/sdk`, `doctrine/annotations`, `ramsey/uuid` and `brick/math`. The
generated client needs Kiota. The SDK does not use the others directly. If your build scopes
or prefixes its vendor files (for example a WordPress plugin), add all of them to the list.
The README section "WordPress / WooCommerce" has the full list.

## The 1.x line

1.x gets security fixes for 6 months after 2.0.0 reaches Packagist. They land on the `1.x`
branch. To stay on it, keep `"puntjes/php-sdk": "^1.7"` in your `composer.json`.
