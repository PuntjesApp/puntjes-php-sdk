<?php

declare(strict_types=1);

namespace Puntjes\Exception;

use Puntjes\Enum\ErrorCode;

/**
 * The API answered with an error envelope:
 *
 *     {"error": {"code": "…", "message": "…", "status": 422, "request_id": "…"}}
 *
 * Subclasses narrow this by HTTP status so callers can catch what they care about;
 * the machine-readable `code` is always available via {@see ErrorCode()}.
 */
class ApiException extends PuntjesException
{
    /**
     * @param  string  $errorCode  Raw wire value of `error.code`.
     * @param  array<string, mixed>|null  $details  `error.details` — validation errors, when present.
     * @param  array<string, mixed>  $body  The full decoded response body.
     */
    public function __construct(
        string $message,
        private readonly int $status,
        private readonly string $errorCode,
        private readonly ?string $requestId = null,
        private readonly ?array $details = null,
        private readonly array $body = [],
    ) {
        parent::__construct($message, $status);
    }

    /** HTTP status code of the response. */
    public function status(): int
    {
        return $this->status;
    }

    /** Raw `error.code` string — always populated, including for codes newer than this SDK. */
    public function code(): string
    {
        return $this->errorCode;
    }

    /** Typed error code, or null when this SDK does not know the code yet. */
    public function errorCode(): ?ErrorCode
    {
        return ErrorCode::tryFromString($this->errorCode);
    }

    /**
     * Server-side correlation id. Quote it when reporting a problem — it is the only
     * handle support has on the failing request.
     */
    public function requestId(): ?string
    {
        return $this->requestId;
    }

    /** @return array<string, mixed>|null */
    public function details(): ?array
    {
        return $this->details;
    }

    /** @return array<string, mixed> */
    public function body(): array
    {
        return $this->body;
    }

    /** True when the failure is this specific domain outcome. */
    public function is(ErrorCode $code): bool
    {
        return $this->errorCode === $code->value;
    }
}
