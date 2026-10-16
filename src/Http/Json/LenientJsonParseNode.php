<?php

declare(strict_types=1);

namespace Puntjes\Http\Json;

use DateInterval;
use DateTime;
use InvalidArgumentException;
use Microsoft\Kiota\Abstractions\Enum;
use Microsoft\Kiota\Abstractions\Serialization\AdditionalDataHolder;
use Microsoft\Kiota\Abstractions\Serialization\ComposedTypeWrapper;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Types\Date;
use Microsoft\Kiota\Abstractions\Types\Time;
use Microsoft\Kiota\Serialization\Json\JsonParseNode;
use Psr\Http\Message\StreamInterface;

/**
 * Kiota's JSON parse node, made tolerant of answers from a newer API: an enum value the generated code does not know
 * reads as null, and its raw string stays in the model's additional data. Primitive reads are Kiota's own.
 */
final class LenientJsonParseNode implements ParseNode
{
    /** @var callable|null */
    private $onBefore = null;

    /** @var callable|null */
    private $onAfter = null;

    private bool $unknownEnum = false;

    public function __construct(private readonly mixed $content) {}

    public function getChildNode(string $identifier): ?ParseNode
    {
        if (! is_array($this->content) || ($this->content[$identifier] ?? null) === null) {
            return null;
        }

        return new self($this->content[$identifier]);
    }

    public function getObjectValue(array $type): ?Parsable
    {
        if ($this->content === null) {
            return null;
        }
        if (! is_subclass_of($type[0], Parsable::class) || ! is_callable($type)) {
            throw new InvalidArgumentException("Invalid type $type[0] provided.");
        }

        $result = $type($this);
        if ($this->onBefore !== null) {
            ($this->onBefore)($result);
        }
        $this->assignFieldValues($result);
        if ($this->onAfter !== null) {
            ($this->onAfter)($result);
        }

        return $result;
    }

    public function getCollectionOfObjectValues(array $type): ?array
    {
        if (! is_array($this->content)) {
            return null;
        }

        return array_filter(array_map(static fn (mixed $item) => (new self($item))->getObjectValue($type), $this->content), static fn ($item) => $item !== null);
    }

    public function getEnumValue(string $targetEnum): ?Enum
    {
        if ($this->content === null) {
            return null;
        }

        $value = self::enum($targetEnum, $this->content);
        $this->unknownEnum = $value === null;

        return $value;
    }

    public function getCollectionOfEnumValues(string $targetClass): ?array
    {
        if (! is_array($this->content)) {
            return null;
        }

        return array_filter(array_map(static fn (mixed $item) => $item === null ? null : self::enum($targetClass, $item), $this->content), static fn ($item) => $item !== null);
    }

    public function getStringValue(): ?string
    {
        return $this->kiota()->getStringValue();
    }

    public function getBooleanValue(): ?bool
    {
        return $this->kiota()->getBooleanValue();
    }

    public function getIntegerValue(): ?int
    {
        return $this->kiota()->getIntegerValue();
    }

    public function getFloatValue(): ?float
    {
        return $this->kiota()->getFloatValue();
    }

    public function getCollectionOfPrimitiveValues(?string $typeName = null): ?array
    {
        return $this->kiota()->getCollectionOfPrimitiveValues($typeName);
    }

    public function getDateTimeValue(): ?DateTime
    {
        return $this->kiota()->getDateTimeValue();
    }

    public function getDateIntervalValue(): ?DateInterval
    {
        return $this->kiota()->getDateIntervalValue();
    }

    public function getDateValue(): ?Date
    {
        return $this->kiota()->getDateValue();
    }

    public function getTimeValue(): ?Time
    {
        return $this->kiota()->getTimeValue();
    }

    public function getBinaryContent(): ?StreamInterface
    {
        return $this->kiota()->getBinaryContent();
    }

    public function getOnBeforeAssignFieldValues(): ?callable
    {
        return $this->onBefore;
    }

    public function getOnAfterAssignFieldValues(): ?callable
    {
        return $this->onAfter;
    }

    public function setOnAfterAssignFieldValues(callable $value): void
    {
        $this->onAfter = $value;
    }

    public function setOnBeforeAssignFieldValues(callable $value): void
    {
        $this->onBefore = $value;
    }

    private function assignFieldValues(Parsable $result): void
    {
        $deserializers = $result->getFieldDeserializers();
        $holder = self::holder($result);
        $additionalData = $holder?->getAdditionalData() ?? [];

        foreach (is_array($this->content) ? $this->content : [] as $key => $value) {
            $deserializer = $deserializers[$key] ?? null;
            if ($deserializer === null) {
                $additionalData[(string) $key] = $value;

                continue;
            }
            $field = new self($value);
            $deserializer($field);
            if ($field->unknownEnum) {
                $additionalData[(string) $key] = $value;
            }
        }

        $holder?->setAdditionalData($additionalData);
    }

    /**
     * The model that keeps keys it has no field for: the model itself, or the member a composed wrapper filled.
     */
    private static function holder(Parsable $result): ?AdditionalDataHolder
    {
        if ($result instanceof AdditionalDataHolder) {
            return $result;
        }
        if (! $result instanceof ComposedTypeWrapper) {
            return null;
        }
        foreach (get_class_methods($result) as $method) {
            $member = str_starts_with($method, 'get') && $method !== 'getFieldDeserializers' ? $result->{$method}() : null;
            if ($member instanceof AdditionalDataHolder) {
                return $member;
            }
        }

        return null;
    }

    /**
     * @param  class-string  $class
     */
    private static function enum(string $class, mixed $value): ?Enum
    {
        if (! is_subclass_of($class, Enum::class)) {
            throw new InvalidArgumentException('Invalid enum provided.');
        }
        if (! is_string($value)) {
            return null;
        }
        try {
            return new $class($value);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    private function kiota(): JsonParseNode
    {
        return new JsonParseNode($this->content);
    }
}
