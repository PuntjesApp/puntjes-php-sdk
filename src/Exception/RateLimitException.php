<?php

declare(strict_types=1);

namespace Puntjes\Exception;

/**
 * 429 RATE_LIMITED — the per-OAuth-client request budget for this minute is spent.
 *
 * The limit is plan-derived (60/min on the free tier) and keyed on the OAuth client,
 * not the IP, so several servers sharing one client share one budget.
 *
 * Deliberately NOT raised for the other 429 the API emits, PLAN_LIMIT_EXCEEDED —
 * see {@see PlanLimitExceededException}. Waiting helps here; it does not there.
 */
final class RateLimitException extends ApiException
{
    /**
     * @param  array<string, mixed>|null  $details
     * @param  array<string, mixed>  $body
     * @param  int|null  $retryAfter  Seconds from the `Retry-After` response header.
     */
    public function __construct(
        string $message,
        int $status,
        string $errorCode,
        ?string $requestId = null,
        ?array $details = null,
        array $body = [],
        private readonly ?int $retryAfter = null,
    ) {
        parent::__construct($message, $status, $errorCode, $requestId, $details, $body);
    }

    /**
     * Seconds to wait before retrying, from the `Retry-After` response header.
     *
     * Null when the server did not send one; back off exponentially in that case.
     */
    public function retryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
