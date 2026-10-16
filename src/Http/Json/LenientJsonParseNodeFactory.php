<?php

declare(strict_types=1);

namespace Puntjes\Http\Json;

use InvalidArgumentException;
use JsonException;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\ParseNodeFactory;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Hands out {@see LenientJsonParseNode} for application/json, in place of Kiota's JsonParseNodeFactory.
 */
final class LenientJsonParseNodeFactory implements ParseNodeFactory
{
    public function getRootParseNode(string $contentType, StreamInterface $rawResponse): ParseNode
    {
        if (strcasecmp($this->getValidContentType(), $contentType) !== 0) {
            throw new InvalidArgumentException("Expected a {$this->getValidContentType()} content type.");
        }
        try {
            return new LenientJsonParseNode(json_decode($rawResponse->getContents(), true, 512, JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            throw new RuntimeException('The body is not valid JSON: '.$e->getMessage(), 0, $e);
        }
    }

    public function getValidContentType(): string
    {
        return 'application/json';
    }
}
