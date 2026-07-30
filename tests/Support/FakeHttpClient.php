<?php

declare(strict_types=1);

namespace Puntjes\Tests\Support;

use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * A scripted PSR-18 client: queue up responses, then assert on what was sent.
 *
 * Deliberately a real PSR-18 implementation rather than a mock object, so tests
 * exercise the same send path a host application would.
 *
 * Token requests are served from their own queue and default to a valid token when
 * that queue is empty. Without that split, every test would have to remember to
 * queue a grant ahead of its actual fixture, and forgetting would show up as a
 * confusing failure about client credentials.
 */
final class FakeHttpClient implements ClientInterface
{
    /** @var array<int, ResponseInterface|ClientExceptionInterface> */
    private array $apiQueue = [];

    /** @var array<int, ResponseInterface> */
    private array $tokenQueue = [];

    /** @var array<int, RequestInterface> */
    public array $requests = [];

    /** @var array<int, string> */
    public array $bodies = [];

    /**
     * @param  array<string, mixed>|list<mixed>|null  $json
     * @param  array<string, string>  $headers
     */
    public function queueJson(int $status, ?array $json, array $headers = []): self
    {
        return $this->queueRaw(
            $status,
            $json === null ? '' : (string) json_encode($json),
            $headers + ['Content-Type' => 'application/json'],
        );
    }

    /** Queue a successful `{"data": …}` envelope. */
    public function queueData(mixed $data, int $status = 200): self
    {
        return $this->queueJson($status, ['data' => $data]);
    }

    /**
     * Queue a paginated `{"data": {"data": [...], "meta": {...}}}` envelope.
     *
     * @param  array<int, mixed>  $items
     */
    public function queuePage(array $items, int $currentPage = 1, int $lastPage = 1, ?int $total = null): self
    {
        return $this->queueData([
            'data' => $items,
            'links' => ['first' => null, 'last' => null, 'prev' => null, 'next' => null],
            'meta' => [
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'per_page' => 15,
                'total' => $total ?? count($items),
            ],
        ]);
    }

    /**
     * Queue an API error envelope.
     *
     * @param  array<string, mixed>|null  $details
     * @param  array<string, string>  $headers
     */
    public function queueError(
        int $status,
        string $code,
        string $message = 'Something went wrong.',
        ?array $details = null,
        array $headers = [],
    ): self {
        $error = [
            'code' => $code,
            'message' => $message,
            'status' => $status,
            'request_id' => 'req_'.$code,
        ];

        if ($details !== null) {
            $error['details'] = $details;
        }

        return $this->queueJson($status, ['error' => $error], $headers);
    }

    /** @param array<string, string> $headers */
    public function queueRaw(int $status, string $body, array $headers = []): self
    {
        $this->apiQueue[] = new Response($status, $headers, $body);

        return $this;
    }

    /** Queue a connection-level failure on the next API request. */
    public function queueNetworkFailure(string $message = 'Connection refused'): self
    {
        $this->apiQueue[] = new FakeNetworkException($message);

        return $this;
    }

    /** Queue the next token grant's response. */
    public function queueToken(string $accessToken = 'test-token', int $expiresIn = 3600): self
    {
        $this->tokenQueue[] = new Response(200, ['Content-Type' => 'application/json'], (string) json_encode([
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
            'access_token' => $accessToken,
        ]));

        return $this;
    }

    /**
     * Queue a failing token grant — the OAuth2 flat error shape, not the API envelope.
     *
     * @param  array<string, mixed>  $json
     */
    public function queueTokenFailure(int $status, array $json): self
    {
        $this->tokenQueue[] = new Response($status, ['Content-Type' => 'application/json'], (string) json_encode($json));

        return $this;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;
        $this->bodies[] = (string) $request->getBody();

        if (str_ends_with($request->getUri()->getPath(), '/oauth/token')) {
            return array_shift($this->tokenQueue) ?? $this->defaultToken();
        }

        if ($this->apiQueue === []) {
            throw new RuntimeException(sprintf(
                'FakeHttpClient ran out of queued API responses at request #%d: %s %s',
                count($this->requests),
                $request->getMethod(),
                (string) $request->getUri(),
            ));
        }

        $next = array_shift($this->apiQueue);

        if ($next instanceof ClientExceptionInterface) {
            throw $next;
        }

        return $next;
    }

    public function requestCount(): int
    {
        return count($this->requests);
    }

    public function requestAt(int $index): RequestInterface
    {
        if (! isset($this->requests[$index])) {
            throw new RuntimeException("No request was sent at index {$index}.");
        }

        return $this->requests[$index];
    }

    /**
     * The decoded JSON body of the nth request.
     *
     * @return array<string, mixed>
     */
    public function bodyAt(int $index): array
    {
        $decoded = json_decode($this->bodies[$index] ?? '', true);

        return is_array($decoded) ? $decoded : [];
    }

    public function uriAt(int $index): string
    {
        return (string) $this->requestAt($index)->getUri();
    }

    /** Requests that were not token grants, in order. @return array<int, RequestInterface> */
    public function apiRequests(): array
    {
        return array_values(array_filter(
            $this->requests,
            static fn (RequestInterface $r): bool => ! str_ends_with($r->getUri()->getPath(), '/oauth/token'),
        ));
    }

    public function apiRequestCount(): int
    {
        return count($this->apiRequests());
    }

    private function defaultToken(): Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], (string) json_encode([
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'access_token' => 'test-token',
        ]));
    }
}
