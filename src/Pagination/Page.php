<?php

declare(strict_types=1);

namespace Puntjes\Pagination;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Puntjes\Http\Response;
use Puntjes\Support\Cast;
use Traversable;

/**
 * One page of a paginated list endpoint.
 *
 * The wire shape nests the paginator inside the standard success envelope, so the
 * items live two levels down:
 *
 *     {"data": {"data": [...], "links": {...}, "meta": {...}}}
 *
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 */
final class Page implements Countable, IteratorAggregate
{
    /**
     * @param  array<int, T>  $items
     * @param  array<string, string|null>  $links
     */
    public function __construct(
        public readonly array $items,
        public readonly PageMeta $meta,
        public readonly array $links = [],
    ) {}

    /**
     * @template TItem
     *
     * @param  callable(array<array-key, mixed>): TItem  $factory
     * @return self<TItem>
     */
    public static function fromResponse(Response $response, callable $factory): self
    {
        $payload = $response->dataArray();

        $links = [];

        foreach (Cast::array($payload, 'links') as $name => $url) {
            $links[(string) $name] = is_string($url) ? $url : null;
        }

        return new self(
            items: Cast::list($payload, 'data', $factory),
            meta: PageMeta::fromArray(Cast::array($payload, 'meta')),
            links: $links,
        );
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function hasMorePages(): bool
    {
        return $this->meta->hasMorePages();
    }

    /** @return T|null */
    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    /** @return Traversable<int, T> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
