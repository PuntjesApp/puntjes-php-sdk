<?php

declare(strict_types=1);

namespace Puntjes\Pagination;

use Generator;
use IteratorAggregate;

/**
 * Lazily walks every page of a list endpoint.
 *
 *     foreach ($puntjes->products->all() as $product) { … }
 *
 * Pages are fetched on demand, one at a time, so a large catalogue never has to fit
 * in memory at once. Iteration stops when the paginator reports the last page — it
 * does not trust an empty page as the end condition, because a page emptied by
 * concurrent deletions is not the same thing as the end of the list.
 *
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 */
final class Paginator implements IteratorAggregate
{
    /** @var callable(int): Page<T> */
    private $fetcher;

    /**
     * @param  callable(int): Page<T>  $fetcher  Receives a 1-based page number.
     */
    public function __construct(callable $fetcher, private readonly int $startPage = 1)
    {
        $this->fetcher = $fetcher;
    }

    /** @return Page<T> */
    public function page(int $number): Page
    {
        return ($this->fetcher)($number);
    }

    /**
     * The first page, without walking any further.
     *
     * @return Page<T>
     */
    public function firstPage(): Page
    {
        return $this->page($this->startPage);
    }

    /**
     * Every page in order.
     *
     * @return Generator<int, Page<T>>
     */
    public function pages(): Generator
    {
        $number = $this->startPage;

        while (true) {
            $page = $this->page($number);

            yield $page;

            if (! $page->hasMorePages()) {
                return;
            }

            $number++;
        }
    }

    /**
     * Every item across every page.
     *
     * @return Generator<int, T>
     */
    public function getIterator(): Generator
    {
        foreach ($this->pages() as $page) {
            yield from $page->items;
        }
    }

    /**
     * Collect every item into one array.
     *
     * Convenient, but it fetches the whole list — prefer iterating for large catalogues.
     *
     * @return array<int, T>
     */
    public function all(): array
    {
        return iterator_to_array($this->getIterator(), false);
    }
}
