<?php

declare(strict_types=1);

namespace Puntjes\Resource;

use Puntjes\Exception\ApiException;
use Puntjes\Exception\ConfigurationException;
use Puntjes\Exception\ConflictException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Model\BatchUpsertResult;
use Puntjes\Model\Product;
use Puntjes\Model\Reward;
use Puntjes\Pagination\Page;
use Puntjes\Pagination\Paginator;
use Puntjes\Request\CreateProduct;
use Puntjes\Request\CreateRewardFromProduct;
use Puntjes\Request\ProductFilters;
use Puntjes\Request\UpdateProduct;
use Puntjes\Request\UpsertProduct;

/**
 * The product catalogue, keyed on your own SKUs.
 *
 * Every route here addresses a product by its `externalId`, never by the Puntjes id,
 * so a POS or webshop can sync its catalogue without storing any Puntjes state.
 */
final class Products extends Resource
{
    /** The largest batch `batchUpsert()` will accept, matching the server's limit. */
    public const MAX_BATCH = 100;

    /**
     * The catalogue, by name, 15 per page by default.
     *
     * @return Paginator<Product>
     */
    public function list(?ProductFilters $filters = null): Paginator
    {
        $query = $filters?->toQuery() ?? [];

        return new Paginator(fn (int $page): Page => Page::fromResponse(
            $this->transport->get('/products', $query + ['page' => $page]),
            Product::fromArray(...),
        ));
    }

    /**
     * Create a product. Responds 201.
     *
     * @throws ConflictException (`PRODUCT_EXTERNAL_ID_DUPLICATE`) when the
     *                           SKU is taken. Prefer {@see upsert()} for sync.
     */
    public function create(CreateProduct $product): Product
    {
        return Product::fromArray(
            $this->transport->post('/products', $product->toArray())->dataArray(),
        );
    }

    /** Fetch one product by SKU. */
    public function find(string $externalId): Product
    {
        return Product::fromArray(
            $this->transport->get('/products/'.$this->segment($externalId))->dataArray(),
        );
    }

    /** As {@see find()}, but null instead of throwing when the SKU is unknown. */
    public function findOrNull(string $externalId): ?Product
    {
        try {
            return $this->find($externalId);
        } catch (NotFoundException) {
            return null;
        }
    }

    /**
     * Create or replace a product by SKU — the catalogue-sync primitive.
     *
     * Pushing the same row repeatedly converges on one product, which is what makes
     * this safe to retry and safe to run on a schedule.
     *
     * Remember that PUT replaces: omitted nullable fields are cleared. Use
     * {@see update()} to touch one field.
     *
     * @return array{product: Product, created: bool} `created` distinguishes the 201 from the 200.
     */
    public function upsert(string $externalId, UpsertProduct $product): array
    {
        $response = $this->transport->put('/products/'.$this->segment($externalId), $product->toArray());

        return [
            'product' => Product::fromArray($response->dataArray()),
            'created' => $response->wasCreated(),
        ];
    }

    /** Partially update a product by SKU. Only supplied fields change. */
    public function update(string $externalId, UpdateProduct $changes): Product
    {
        return Product::fromArray(
            $this->transport->patch('/products/'.$this->segment($externalId), $changes->toArray())->dataArray(),
        );
    }

    /**
     * Soft-delete a product by SKU. Responds 204.
     *
     * @throws NotFoundException when the SKU is unknown. Unlike the upsert, this is
     *                           not idempotent from the caller's point of view — a
     *                           second delete 404s.
     */
    public function delete(string $externalId): void
    {
        $this->transport->delete('/products/'.$this->segment($externalId));
    }

    /**
     * Upsert up to 100 products in one call — the workhorse for a full catalogue push.
     *
     * Every item is processed independently, so one bad row does not fail the batch
     * and the call answers 200 even when every item failed. ALWAYS inspect the
     * result: `$result->hasFailures()` is the only signal that something went wrong.
     *
     * Not auto-retried by the transport (it is a POST without an idempotency key),
     * though the upserts it performs are themselves convergent — retry it yourself
     * if you need to.
     *
     * @param  array<string, UpsertProduct>  $products  Keyed by external id (SKU).
     *
     * @throws ConfigurationException when the batch is empty or over the limit,
     *                                caught here rather than spending a request on a
     *                                `BATCH_TOO_LARGE` the client can predict.
     */
    public function batchUpsert(array $products): BatchUpsertResult
    {
        if ($products === []) {
            throw new ConfigurationException('A product batch must contain at least one product.');
        }

        if (count($products) > self::MAX_BATCH) {
            throw new ConfigurationException(sprintf(
                'A product batch may contain at most %d products, got %d. Chunk the catalogue.',
                self::MAX_BATCH,
                count($products),
            ));
        }

        $items = [];

        foreach ($products as $externalId => $product) {
            $items[] = $product->toBatchArray((string) $externalId);
        }

        return BatchUpsertResult::fromArray(
            $this->transport->post('/products/batch', ['products' => $items])->dataArray(),
        );
    }

    /**
     * Turn a catalogue product into a redeemable `free_product` reward. Responds 201.
     *
     * @throws NotFoundException when the SKU is unknown.
     * @throws ApiException on validation failure.
     */
    public function createReward(string $externalId, CreateRewardFromProduct $reward): Reward
    {
        return Reward::fromArray(
            $this->transport->post(
                '/products/'.$this->segment($externalId).'/reward',
                $reward->toArray(),
            )->dataArray(),
        );
    }
}
