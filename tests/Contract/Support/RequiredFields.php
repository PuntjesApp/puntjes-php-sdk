<?php

declare(strict_types=1);

namespace Puntjes\Tests\Contract\Support;

/**
 * Names every property the spec marks required that an API answer leaves out.
 *
 * A test reads a few fields of each answer; this reads all of them, so a removed field fails the
 * contract suite even when no test asserts on it. It checks presence only: a null value is present.
 */
final class RequiredFields
{
    /** @param array<string, mixed> $spec */
    public function __construct(private readonly array $spec) {}

    public static function fromSpecFile(): self
    {
        return new self(json_decode((string) file_get_contents(__DIR__.'/../../../spec/api.json'), true, flags: JSON_THROW_ON_ERROR));
    }

    /**
     * @param  string  $path  The request path below the spec's server URL, such as `/products/sku-1`
     * @return list<string>
     */
    public function missing(string $method, string $path, int $status, mixed $body): array
    {
        $label = strtoupper($method).' '.$path;
        $operation = $this->operation(strtolower($method), $path);

        if ($operation === null) {
            return ["{$label}: no operation in spec/api.json"];
        }

        $schema = $operation['responses'][(string) $status]['content']['application/json']['schema'] ?? null;

        if (! is_array($schema)) {
            return [];
        }

        return array_map(fn (string $gap): string => "{$label}: {$gap}", $this->check($schema, $body, ''));
    }

    /** @return array<string, mixed>|null */
    private function operation(string $verb, string $path): ?array
    {
        if (isset($this->spec['paths'][$path][$verb])) {
            return $this->spec['paths'][$path][$verb];
        }

        $segments = explode('/', $path);

        foreach ($this->spec['paths'] as $template => $operations) {
            if (isset($operations[$verb]) && self::matches(explode('/', $template), $segments)) {
                return $operations[$verb];
            }
        }

        return null;
    }

    /**
     * @param  list<string>  $template
     * @param  list<string>  $segments
     */
    private static function matches(array $template, array $segments): bool
    {
        if (count($template) !== count($segments)) {
            return false;
        }

        foreach ($template as $i => $part) {
            if ($part !== $segments[$i] && ! str_starts_with($part, '{')) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $schema
     * @return list<string>
     */
    private function check(array $schema, mixed $value, string $at): array
    {
        $schema = $this->resolve($schema);

        foreach (['oneOf', 'anyOf'] as $union) {
            if (isset($schema[$union])) {
                return $this->checkUnion($schema, $union, $value, $at);
            }
        }

        $gaps = [];

        foreach ($schema['allOf'] ?? [] as $part) {
            array_push($gaps, ...$this->check($part, $value, $at));
        }

        if (! is_array($value)) {
            return $gaps;
        }

        if (isset($schema['items'])) {
            foreach ($value as $i => $item) {
                array_push($gaps, ...$this->check($schema['items'], $item, "{$at}[{$i}]"));
            }

            return $gaps;
        }

        foreach ($schema['required'] ?? [] as $key) {
            if (! array_key_exists($key, $value)) {
                $gaps[] = self::join($at, $key);
            }
        }

        foreach ($schema['properties'] ?? [] as $key => $property) {
            if (array_key_exists($key, $value)) {
                array_push($gaps, ...$this->check($property, $value[$key], self::join($at, $key)));
            }
        }

        return $gaps;
    }

    /**
     * @param  array<string, mixed>  $schema
     * @return list<string>
     */
    private function checkUnion(array $schema, string $union, mixed $value, string $at): array
    {
        $tag = is_array($value) ? ($value[$schema['discriminator']['propertyName'] ?? ''] ?? null) : null;
        $mapped = is_string($tag) ? ($schema['discriminator']['mapping'][$tag] ?? null) : null;

        if (is_string($mapped)) {
            return $this->check(['$ref' => $mapped], $value, $at);
        }

        foreach ($schema[$union] as $branch) {
            if ($this->check($branch, $value, $at) === []) {
                return [];
            }
        }

        return [($at === '' ? 'the answer' : $at)." matches no branch of its {$union}"];
    }

    /**
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    private function resolve(array $schema): array
    {
        while (isset($schema['$ref'])) {
            $schema = $this->spec['components']['schemas'][substr($schema['$ref'], strlen('#/components/schemas/'))];
        }

        return $schema;
    }

    private static function join(string $at, string $key): string
    {
        return $at === '' ? $key : "{$at}.{$key}";
    }
}
