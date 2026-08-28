<?php

declare(strict_types=1);

namespace Puntjes\Support;

/**
 * Defensive readers for decoded JSON.
 *
 * Response models are built from arrays that crossed a network, so every field is
 * `mixed` until proven otherwise. These keep the models free of repetitive
 * `is_string(...) ? ... : null` noise while still refusing to invent data: a missing
 * field reads as null (or the supplied default), never as `0` or `''`.
 *
 * @internal
 */
final class Cast
{
    /** @param array<array-key, mixed> $data */
    public static function int(array $data, string $key, int $default = 0): int
    {
        $value = $data[$key] ?? null;

        return is_numeric($value) ? (int) $value : $default;
    }

    /** @param array<array-key, mixed> $data */
    public static function nullableInt(array $data, string $key): ?int
    {
        $value = $data[$key] ?? null;

        return is_numeric($value) ? (int) $value : null;
    }

    /** @param array<array-key, mixed> $data */
    public static function float(array $data, string $key, float $default = 0.0): float
    {
        $value = $data[$key] ?? null;

        return is_numeric($value) ? (float) $value : $default;
    }

    /** @param array<array-key, mixed> $data */
    public static function nullableFloat(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;

        return is_numeric($value) ? (float) $value : null;
    }

    /** @param array<array-key, mixed> $data */
    public static function string(array $data, string $key, string $default = ''): string
    {
        $value = $data[$key] ?? null;

        return is_string($value) ? $value : (is_scalar($value) ? (string) $value : $default);
    }

    /** @param array<array-key, mixed> $data */
    public static function nullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        if ($value === null) {
            return null;
        }

        return is_string($value) ? $value : (is_scalar($value) ? (string) $value : null);
    }

    /** @param array<array-key, mixed> $data */
    public static function bool(array $data, string $key, bool $default = false): bool
    {
        $value = $data[$key] ?? null;

        return is_bool($value) ? $value : (is_scalar($value) ? (bool) $value : $default);
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    public static function array(array $data, string $key): array
    {
        $value = $data[$key] ?? null;

        return is_array($value) ? $value : [];
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>|null
     */
    public static function nullableArray(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;

        return is_array($value) ? $value : null;
    }

    /**
     * Map a list of raw rows through a model factory, skipping anything that is not a row.
     *
     * @template T
     *
     * @param  array<array-key, mixed>  $data
     * @param  callable(array<array-key, mixed>): T  $factory
     * @return array<int, T>
     */
    public static function list(array $data, string $key, callable $factory): array
    {
        $items = [];

        foreach (self::array($data, $key) as $row) {
            if (is_array($row)) {
                $items[] = $factory($row);
            }
        }

        return $items;
    }

    /**
     * As {@see list()}, but preserving the difference between an absent list and an
     * empty one. Several fields on this API mean opposite things by the two — a
     * `branches` scope of null runs everywhere, an empty one matches nothing.
     *
     * @template T
     *
     * @param  array<array-key, mixed>  $data
     * @param  callable(array<array-key, mixed>): T  $factory
     * @return array<int, T>|null
     */
    public static function nullableList(array $data, string $key, callable $factory): ?array
    {
        $rows = self::nullableArray($data, $key);

        if ($rows === null) {
            return null;
        }

        $items = [];

        foreach ($rows as $row) {
            if (is_array($row)) {
                $items[] = $factory($row);
            }
        }

        return $items;
    }

    /**
     * Nested object, or null when absent.
     *
     * @template T
     *
     * @param  array<array-key, mixed>  $data
     * @param  callable(array<array-key, mixed>): T  $factory
     * @return T|null
     */
    public static function object(array $data, string $key, callable $factory): mixed
    {
        $value = $data[$key] ?? null;

        return is_array($value) ? $factory($value) : null;
    }

    /**
     * Read a `{value, label}` status object, returning just the value.
     *
     * Several response models nest status this way (`CustomerStatusData`,
     * `RewardStatusData`, `CampaignStatusData`) while others send a bare string.
     * Accept both so one model works against either shape.
     *
     * @param  array<array-key, mixed>  $data
     */
    public static function statusValue(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        if (is_array($value)) {
            return self::nullableString($value, 'value');
        }

        return is_string($value) ? $value : null;
    }
}
