<?php

declare(strict_types=1);

[$self, $tag, $level, $url, $source, $open] = $argv + [null, '', '', '', '', ''];

if (preg_match('/^v\d+\.\d+\.\d+$/', (string) $tag) !== 1) {
    fwrite(STDERR, "The tag {$tag} is not vX.Y.Z.\n");
    exit(1);
}
if ($url !== "https://raw.githubusercontent.com/PuntjesApp/puntjes-openapi/{$tag}/api.json") {
    fwrite(STDERR, "The url is not api.json of puntjes-openapi at {$tag}.\n");
    exit(1);
}
$type = ['major' => 'feat!', 'minor' => 'feat', 'patch' => 'fix'][$level] ?? null;
if ($type === null) {
    fwrite(STDERR, "The level {$level} is not major, minor or patch.\n");
    exit(1);
}

$version = static fn (string $ref): ?string => preg_match('/^v(\d+\.\d+\.\d+)$/', $ref, $m) === 1 ? $m[1] : null;
$current = $version($tag);
$sourced = $version(trim((string) $source));
$openTags = [];
foreach (preg_split('/\s+/', (string) $open, -1, PREG_SPLIT_NO_EMPTY) as $branch) {
    $openVersion = str_starts_with($branch, 'regenerate/') ? $version(substr($branch, 11)) : null;
    if ($openVersion !== null) {
        $openTags[$branch] = $openVersion;
    }
}
$newerOpen = array_filter($openTags, static fn (string $v) => version_compare($v, $current, '>'));
$skip = ($sourced !== null && version_compare($sourced, $current, '>=')) || $newerOpen !== [];
$close = $skip ? [] : array_keys(array_filter($openTags, static fn (string $v) => version_compare($v, $current, '<')));

echo 'skip='.($skip ? 'true' : 'false')."\n";
echo "commit_type={$type}\n";
echo 'auto_merge='.($level === 'major' ? 'false' : 'true')."\n";
echo "branch=regenerate/{$tag}\n";
echo 'close='.implode(' ', $close)."\n";
