<?php

declare(strict_types=1);

namespace Puntjes\Exception;

/**
 * 409 — the value is already taken: a duplicate loyalty identifier
 * (IDENTIFIER_DUPLICATE), customer external id (EXTERNAL_ID_DUPLICATE) or product
 * SKU (PRODUCT_EXTERNAL_ID_DUPLICATE).
 */
final class ConflictException extends ApiException {}
