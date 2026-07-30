<?php

declare(strict_types=1);

namespace Puntjes\Http;

use Puntjes\Enum\ErrorCode;
use Puntjes\Exception\ApiException;
use Puntjes\Exception\AuthenticationException;
use Puntjes\Exception\ConflictException;
use Puntjes\Exception\ForbiddenException;
use Puntjes\Exception\NotFoundException;
use Puntjes\Exception\PlanLimitExceededException;
use Puntjes\Exception\RateLimitException;
use Puntjes\Exception\ServerException;
use Puntjes\Exception\TransportException;
use Puntjes\Exception\ValidationException;

/**
 * Turns a non-2xx response into the right typed exception.
 *
 * The API always answers errors with
 * `{"error": {"code", "message", "status", "request_id", "details"?}}`, so the
 * mapping keys off the HTTP status first and the domain code second.
 */
final class ErrorMapper
{
    /**
     * @throws TransportException when the body is not JSON and the status is below
     *                            500 — the SDK is not talking to the Puntjes API at
     *                            all, and no retry or error code applies.
     */
    public static function toException(Response $response): ApiException
    {
        $body = $response->tryJson();

        if ($body === null) {
            // A non-JSON 5xx is almost always a proxy or load balancer answering for a
            // backend that is down — the most common transient failure in production.
            // Type it as a server error so the retry policy treats it like one. The
            // NON_JSON_RESPONSE code is synthetic (this SDK's, not the API's), so it is
            // deliberately absent from the ErrorCode enum.
            if ($response->status >= 500) {
                return new ServerException(
                    sprintf(
                        'The Puntjes API returned HTTP %d with a non-JSON body — likely a proxy or load balancer answering for a failing backend: %s',
                        $response->status,
                        $response->bodyExcerpt(),
                    ),
                    $response->status,
                    'NON_JSON_RESPONSE',
                );
            }

            // Below 500, a non-JSON body means the SDK is not talking to the Puntjes
            // API at all (wrong base URL, a login redirect, a WAF page). Say so plainly
            // rather than inventing an error code — and never retry it.
            throw new TransportException(sprintf(
                'The Puntjes API returned a non-JSON error response (HTTP %d): %s',
                $response->status,
                $response->bodyExcerpt(),
            ));
        }

        $error = is_array($body['error'] ?? null) ? $body['error'] : [];

        $code = is_string($error['code'] ?? null) ? $error['code'] : 'UNKNOWN_ERROR';
        $message = is_string($error['message'] ?? null)
            ? $error['message']
            : sprintf('The Puntjes API returned HTTP %d.', $response->status);
        $requestId = is_string($error['request_id'] ?? null) ? $error['request_id'] : null;
        $details = is_array($error['details'] ?? null) ? $error['details'] : null;

        $status = $response->status;

        // 429 carries two unrelated failures. PLAN_LIMIT_EXCEEDED is a billing stop that
        // no amount of waiting clears, so it must never be confused with rate limiting.
        if ($status === 429 && $code === ErrorCode::PlanLimitExceeded->value) {
            return new PlanLimitExceededException($message, $status, $code, $requestId, $details, $body);
        }

        if ($status === 429) {
            return new RateLimitException(
                $message,
                $status,
                $code,
                $requestId,
                $details,
                $body,
                self::retryAfterSeconds($response),
            );
        }

        // 422 is both validation failure and domain refusal (INSUFFICIENT_BALANCE,
        // OUT_OF_STOCK, CUSTOMER_DEACTIVATED, IDEMPOTENCY_KEY_CONFLICT, …). Only the
        // former carries a field-keyed `details` map worth a dedicated type.
        if ($status === 422 && $code === ErrorCode::ValidationError->value) {
            return new ValidationException($message, $status, $code, $requestId, $details, $body);
        }

        $class = match (true) {
            $status === 401 => AuthenticationException::class,
            $status === 403 => ForbiddenException::class,
            $status === 404 => NotFoundException::class,
            $status === 409 => ConflictException::class,
            $status >= 500 => ServerException::class,
            default => ApiException::class,
        };

        return new $class($message, $status, $code, $requestId, $details, $body);
    }

    /** `Retry-After` as seconds. Laravel's throttler sends a delta, not an HTTP date. */
    public static function retryAfterSeconds(Response $response): ?int
    {
        $header = $response->header('retry-after');

        if ($header === null) {
            return null;
        }

        if (is_numeric($header)) {
            return max(0, (int) $header);
        }

        $timestamp = strtotime($header);

        return $timestamp === false ? null : max(0, $timestamp - time());
    }
}
