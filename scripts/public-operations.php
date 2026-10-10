<?php

declare(strict_types=1);

[$self, $specPath, $outPath] = $argv + [null, null, null];
$spec = json_decode((string) file_get_contents((string) $specPath), true, flags: JSON_THROW_ON_ERROR);
$public = [];

foreach ($spec['paths'] ?? [] as $path => $operations) {
    foreach ($operations as $method => $operation) {
        if (is_array($operation) && array_key_exists('security', $operation) && $operation['security'] === []) {
            $public[] = strtoupper($method).' '.$path;
        }
    }
}
sort($public);

$list = implode("\n", array_map(fn (string $operation): string => '        '.var_export($operation, true).',', $public));

file_put_contents((string) $outPath, <<<PHP
<?php

declare(strict_types=1);

namespace Puntjes\Generated\Security;

/**
 * The operations whose security in spec/api.json is empty, so they are sent without a token. Generated. Do not edit.
 */
final class PublicOperations
{
    public const OPERATIONS = [
{$list}
    ];

    public static function matches(string \$method, string \$path): bool
    {
        foreach (self::OPERATIONS as \$operation) {
            [\$publicMethod, \$template] = explode(' ', \$operation, 2);
            if (strcasecmp(\$method, \$publicMethod) === 0 && self::fits(explode('/', \$template), explode('/', \$path))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<string>  \$template
     * @param  list<string>  \$path
     */
    private static function fits(array \$template, array \$path): bool
    {
        if (count(\$template) !== count(\$path)) {
            return false;
        }
        foreach (\$template as \$i => \$segment) {
            \$isParameter = str_starts_with(\$segment, '{') && str_ends_with(\$segment, '}');
            if (\$isParameter ? \$path[\$i] === '' : \$path[\$i] !== \$segment) {
                return false;
            }
        }

        return true;
    }
}

PHP);
