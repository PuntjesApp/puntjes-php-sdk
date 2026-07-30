<?php

declare(strict_types=1);

namespace Puntjes\Enum;

/** Lifecycle state of a customer record. */
enum CustomerStatus: string
{
    case Active = 'active';
    case Deactivated = 'deactivated';

    /**
     * GDPR erasure has been applied. Never observed through the API: anonymized
     * customers are reported as not found so their prior existence is not revealed.
     */
    case Anonymized = 'anonymized';

    public function canTransact(): bool
    {
        return $this === self::Active;
    }
}
