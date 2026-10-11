<?php

declare(strict_types=1);

[$self, $specPath, $outPath] = $argv + [null, null, null];
$spec = json_decode((string) file_get_contents((string) $specPath), true, flags: JSON_THROW_ON_ERROR);
$codes = [];

$walk = function (mixed $node) use (&$walk, &$codes): void {
    if (! is_array($node)) {
        return;
    }
    foreach ($node['x-error-codes'] ?? [] as $entry) {
        $known = $codes[$entry['code']] ?? null;
        if ($known !== null && $known !== $entry['status']) {
            fwrite(STDERR, "{$entry['code']} has two statuses, {$known} and {$entry['status']}\n");
            exit(1);
        }
        $codes[$entry['code']] = (int) $entry['status'];
    }
    foreach ($node as $child) {
        $walk($child);
    }
};
$walk($spec);
ksort($codes);

$case = fn (string $code): string => str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $code))));
$cases = implode("\n", array_map(fn ($code) => "    case {$case($code)} = '{$code}';", array_keys($codes)));
$statuses = implode("\n", array_map(fn ($code, $status) => "            self::{$case($code)} => {$status},", array_keys($codes), $codes));

file_put_contents((string) $outPath, <<<PHP
<?php

declare(strict_types=1);

namespace Puntjes\Generated\Errors;

/**
 * Every error code the API documents, generated from the x-error-codes of spec/api.json. Do not edit.
 */
enum ErrorCode: string
{
{$cases}

    public function status(): int
    {
        return match (\$this) {
{$statuses}
        };
    }

    public static function tryFromString(?string \$code): ?self
    {
        return \$code === null ? null : self::tryFrom(\$code);
    }
}

PHP);
