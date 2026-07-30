<?php

declare(strict_types=1);

namespace Puntjes\Http;

use Puntjes\Exception\TransportException;

/**
 * A received HTTP response, decoded lazily.
 *
 * Deliberately not a PSR-7 `ResponseInterface`: bodies are streams that can only be
 * read once, and the retry loop plus the error mapper both need to look at them.
 */
final class Response
{
    /** @var array<string, mixed>|null */
    private ?array $decoded = null;

    private bool $decodeAttempted = false;

    /**
     * @param  array<string, string>  $headers  Header names lowercased.
     */
    public function __construct(
        public readonly int $status,
        public readonly array $headers,
        public readonly string $body,
    ) {}

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    public function isSuccessful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function wasCreated(): bool
    {
        return $this->status === 201;
    }

    /**
     * The decoded JSON body.
     *
     * @return array<string, mixed>
     *
     * @throws TransportException when the body is not a JSON object.
     */
    public function json(): array
    {
        $decoded = $this->tryJson();

        if ($decoded === null) {
            throw new TransportException(sprintf(
                'Expected a JSON response from the Puntjes API but got %s (HTTP %d): %s',
                $this->header('content-type') ?? 'no content type',
                $this->status,
                $this->bodyExcerpt(),
            ));
        }

        return $decoded;
    }

    /**
     * The decoded body, or null when it is absent or not a JSON object.
     *
     * @return array<string, mixed>|null
     */
    public function tryJson(): ?array
    {
        if ($this->decodeAttempted) {
            return $this->decoded;
        }

        $this->decodeAttempted = true;

        if (trim($this->body) === '') {
            return $this->decoded = null;
        }

        $decoded = json_decode($this->body, true);

        return $this->decoded = is_array($decoded) ? $decoded : null;
    }

    /**
     * The payload inside the `{"data": …}` success envelope.
     *
     * Every successful JSON response from the API is wrapped this way, so a body
     * without it means the SDK is talking to something that is not the Puntjes API
     * (a proxy error page, a login redirect, a wrong base URL).
     */
    public function data(): mixed
    {
        $json = $this->json();

        if (! array_key_exists('data', $json)) {
            throw new TransportException(sprintf(
                'The Puntjes API response (HTTP %d) had no "data" envelope: %s',
                $this->status,
                $this->bodyExcerpt(),
            ));
        }

        return $json['data'];
    }

    /**
     * The `data` payload as an array.
     *
     * @return array<array-key, mixed>
     */
    public function dataArray(): array
    {
        $data = $this->data();

        if (! is_array($data)) {
            throw new TransportException(sprintf(
                'Expected the Puntjes API "data" envelope to hold an object or list, got %s.',
                get_debug_type($data),
            ));
        }

        return $data;
    }

    /** First 200 characters of the body, for error messages. */
    public function bodyExcerpt(int $length = 200): string
    {
        $trimmed = trim($this->body);

        if ($trimmed === '') {
            return '(empty body)';
        }

        return strlen($trimmed) > $length
            ? substr($trimmed, 0, $length).'…'
            : $trimmed;
    }
}
