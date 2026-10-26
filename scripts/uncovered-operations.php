<?php

declare(strict_types=1);

[$self, $spec] = $argv + [null, ''];
$suite = array_slice($argv, 2);

$decoded = json_decode((string) @file_get_contents((string) $spec), true);
if (! is_array($decoded) || ! is_array($decoded['paths'] ?? null) || $suite === []) {
    fwrite(STDERR, "Usage: php scripts/uncovered-operations.php <spec json> <contract test file>...\n");
    exit(1);
}

$operations = [];
foreach ($decoded['paths'] as $verbs) {
    foreach ($verbs as $verb => $operation) {
        if (in_array($verb, ['get', 'post', 'put', 'patch', 'delete'], true)) {
            $operations[] = $operation['operationId'];
        }
    }
}

$code = '';
foreach ($suite as $file) {
    foreach (PhpToken::tokenize((string) file_get_contents($file)) as $token) {
        if (! $token->is([T_COMMENT, T_DOC_COMMENT, T_INLINE_HTML])) {
            $code .= $token->text;
        }
    }
}
preg_match_all("/Live::hit\(self::CLIENT, '([A-Za-z]+)'\)/", $code, $hits);

foreach (array_diff($operations, $hits[1]) as $operation) {
    echo $operation, PHP_EOL;
}
