<?php

declare(strict_types=1);

namespace Puntjes\Exception;

/**
 * 429 PLAN_LIMIT_EXCEEDED — the vendor has recorded more transactions this billing
 * period than its subscription plan allows (plan maximum plus its overage
 * threshold), so `POST /transactions` is hard-stopped.
 *
 * It shares the 429 status with {@see RateLimitException} but is a different
 * failure: no amount of waiting clears it within the period. The SDK therefore
 * never auto-retries it — surface it to a human and upgrade the plan.
 */
final class PlanLimitExceededException extends ApiException {}
