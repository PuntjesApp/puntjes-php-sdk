<?php

declare(strict_types=1);

namespace Puntjes\Exception;

/**
 * 404 — the customer, product, reward or redemption does not exist within
 * the calling vendor. Also returned for a resource owned by a different vendor, so
 * a cross-tenant probe is indistinguishable from a genuine miss.
 */
final class NotFoundException extends ApiException {}
