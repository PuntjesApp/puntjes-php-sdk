<?php

declare(strict_types=1);

namespace Puntjes\Exception;

use RuntimeException;

/**
 * Base type for every error this SDK raises.
 *
 * Catch this to handle "anything Puntjes-related went wrong" in one place.
 */
class PuntjesException extends RuntimeException {}
