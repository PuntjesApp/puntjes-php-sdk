<?php

declare(strict_types=1);

namespace Puntjes\Enum;

/** Catalogue visibility of a product. */
enum ProductStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';
}
