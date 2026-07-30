<?php

declare(strict_types=1);

namespace Puntjes\Support;

/**
 * "This field was not supplied" — distinct from "this field was supplied as null".
 *
 * PATCH endpoints (`/customers/by-external-id/{id}`, `/products/{sku}`) change only
 * the fields present in the body, and several of those fields are nullable. Without
 * a third state, an SDK cannot express "clear the customer's phone number"
 * (`phone: null`) separately from "leave the phone number alone" (omit it).
 *
 * Mirrors `Spatie\LaravelData\Optional` on the server side.
 */
final class Undefined
{
    /**
     * Constructed, not a singleton, so it can be written inline as a parameter
     * default (`= new Undefined`) — PHP allows `new` in initializers but not static
     * calls. Instances are interchangeable; identity is never compared, only type.
     */
    public function __construct() {}

    public static function is(mixed $value): bool
    {
        return $value instanceof self;
    }

    /**
     * Drop every Undefined entry, leaving explicit nulls in place.
     *
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public static function prune(array $values): array
    {
        return array_filter($values, static fn (mixed $value): bool => ! self::is($value));
    }
}
