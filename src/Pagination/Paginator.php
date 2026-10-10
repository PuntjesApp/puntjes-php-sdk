<?php

declare(strict_types=1);

namespace Puntjes\Pagination;

use Generator;

/**
 * Walks a paginated call page by page; the API wraps each page as data.data with data.meta.
 */
final class Paginator
{
    /**
     * @param  callable(int): object  $fetch  Returns the generated response for one page number
     * @return Generator<int, mixed, mixed, void>
     */
    public static function of(callable $fetch): Generator
    {
        $page = 1;
        do {
            $wrapper = $fetch($page)->getData();
            foreach ($wrapper->getData() ?? [] as $item) {
                yield $item;
            }
            $meta = $wrapper->getMeta();
            $page++;
        } while ($meta !== null && $meta->getCurrentPage() < $meta->getLastPage());
    }
}
