<?php

declare(strict_types=1);

// Kiota makes every request body a required argument, also when the spec does not say `required: true`
// (microsoft/kiota#8160). This makes such a body optional: `post()` sends no body, `post($body)` sends it.
// It fails on any generated shape it does not expect, so a Kiota change cannot skip a body without a sound.

[$self, $specPath, $generatedDir] = $argv + [null, null, null];
$spec = json_decode((string) file_get_contents((string) $specPath), true, flags: JSON_THROW_ON_ERROR);

function fail(string $message): never
{
    fwrite(STDERR, "optional-bodies: {$message}\n");
    exit(1);
}

/** @return array<string, list<string>> */
function buildersByPath(string $dir): array
{
    $builders = [];
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)) as $file) {
        if (str_ends_with($file->getFilename(), 'RequestBuilder.php')
            && preg_match('/^ \* Builds and executes requests for operations under (\S+)$/m', (string) file_get_contents($file->getPathname()), $m)) {
            $builders[$m[1]][] = $file->getPathname();
        }
    }

    return $builders;
}

function replaceOnce(string $pattern, string $replacement, string $subject, string $where): string
{
    $result = preg_replace($pattern, $replacement, $subject, -1, $count);
    if ($count !== 1) {
        fail("{$where}: expected 1 match of {$pattern}, found {$count}.");
    }

    return (string) $result;
}

function makeBodyOptional(string $source, string $method, bool $setsContent, string $where): string
{
    $name = preg_quote($method, '/');
    $chunk = '/    \/\*\*\n(?:     \*.*\n)*    \*\/\n    public function '.$name.'\((?:.*\n)*?    }\n/';
    if (preg_match_all($chunk, $source, $found) !== 1) {
        fail("{$where}: expected one method {$method}().");
    }
    $old = $found[0][0];
    $new = replaceOnce('/(@param )(\w+)( \$body The request body)/', '$1$2|null$3', $old, "{$where} {$method}()");
    $new = replaceOnce('/(public function '.$name.'\()(\w+) \$body, /', '$1?$2 $body = null, ', $new, "{$where} {$method}()");
    if ($setsContent) {
        $new = replaceOnce(
            '/^        (\$requestInfo->setContentFrom\w+\(.*\$body\);)\n/m',
            "        if (\$body !== null) {\n            \$1\n        }\n",
            $new,
            "{$where} {$method}()",
        );
    }

    return str_replace($old, $new, $source);
}

$builders = buildersByPath((string) $generatedDir);
$changed = 0;

foreach ($spec['paths'] ?? [] as $path => $operations) {
    foreach ($operations as $method => $operation) {
        if (! is_array($operation) || ! isset($operation['requestBody']) || ($operation['requestBody']['required'] ?? false) === true) {
            continue;
        }
        $where = strtoupper($method).' '.$path;
        $files = $builders[$path] ?? [];
        if (count($files) !== 1) {
            fail("{$where}: expected one request builder, found ".count($files).'.');
        }
        $verb = ucfirst(strtolower($method));
        $source = (string) file_get_contents($files[0]);
        $source = makeBodyOptional($source, strtolower($method), false, $where);
        $source = makeBodyOptional($source, "to{$verb}RequestInformation", true, $where);
        file_put_contents($files[0], $source);
        $changed++;
    }
}

echo "optional-bodies: made {$changed} request bodies optional.\n";
