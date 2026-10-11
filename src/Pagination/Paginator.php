<?php

declare(strict_types=1);

namespace Puntjes\Pagination;

use Generator;

/**
 * Walks a paginated call page by page; the API wraps each page as data.data with data.meta.
 * It stops at the last page, and when an answer has no page number or not the page it asked for.
 */
final class Paginator
{
    /**
     * @param  callable(int): object  $fetch  Returns the generated response for one page number
     * @return Generator<int, mixed, mixed, void>
     */
    public static function of(callable $fetch): Generator
    {
        for ($page = 1; ; $page++) {
            $wrapper = $fetch($page)->getData();
            $meta = $wrapper->getMeta();
            $current = $meta?->getCurrentPage();
            if ($page > 1 && $current !== $page) {
                return;
            }
            foreach ($wrapper->getData() ?? [] as $item) {
                yield $item;
            }
            if ($current !== $page || $page >= $meta?->getLastPage()) {
                return;
            }
        }
    }
}
