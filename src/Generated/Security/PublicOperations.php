<?php

declare(strict_types=1);

namespace Puntjes\Generated\Security;

/**
 * The operations whose security in spec/api.json is empty, so they are sent without a token. Generated. Do not edit.
 */
final class PublicOperations
{
    public const OPERATIONS = [
        'GET /health',
    ];

    public static function matches(string $method, string $path): bool
    {
        foreach (self::OPERATIONS as $operation) {
            [$publicMethod, $template] = explode(' ', $operation, 2);
            if (strcasecmp($method, $publicMethod) === 0 && self::fits(explode('/', $template), explode('/', $path))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<string>  $template
     * @param  list<string>  $path
     */
    private static function fits(array $template, array $path): bool
    {
        if (count($template) !== count($path)) {
            return false;
        }
        foreach ($template as $i => $segment) {
            $isParameter = str_starts_with($segment, '{') && str_ends_with($segment, '}');
            if ($isParameter ? $path[$i] === '' : $path[$i] !== $segment) {
                return false;
            }
        }

        return true;
    }
}
