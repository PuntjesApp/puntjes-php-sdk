<?php

declare(strict_types=1);

namespace Puntjes\Exception;

/**
 * 403 — authenticated, but not allowed. Almost always the vendor account
 * state: VENDOR_PENDING, VENDOR_SUSPENDED or VENDOR_DEACTIVATED.
 */
final class ForbiddenException extends ApiException {}
