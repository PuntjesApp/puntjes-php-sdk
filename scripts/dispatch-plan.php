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
if (! in_array($level, ['major', 'minor', 'patch'], true)) {
    fwrite(STDERR, "The level {$level} is not major, minor or patch.\n");
    exit(1);
}

$version = static fn (string $ref): ?string => preg_match('/^v(\d+\.\d+\.\d+)$/', $ref, $m) === 1 ? $m[1] : null;
$current = $version($tag);
$sourced = $version(trim((string) $source));
$openTags = [];
$openMajors = [];
foreach (preg_split('/\R/', (string) $open, -1, PREG_SPLIT_NO_EMPTY) as $line) {
    [$branch, $title] = array_pad(preg_split('/\s+/', trim($line), 2), 2, '');
    $openVersion = str_starts_with($branch, 'regenerate/') ? $version(substr($branch, 11)) : null;
    if ($openVersion !== null) {
        $openTags[$branch] = $openVersion;
        $openMajors[$branch] = str_starts_with($title, 'feat!:');
    }
}
$newerOpen = array_filter($openTags, static fn (string $v) => version_compare($v, $current, '>'));
$skip = ($sourced !== null && version_compare($sourced, $current, '>=')) || $newerOpen !== [];
$close = $skip ? [] : array_keys(array_filter($openTags, static fn (string $v) => version_compare($v, $current, '<')));
$major = $level === 'major'
    || array_filter($close, static fn (string $branch) => $openMajors[$branch]) !== []
    || ($sourced !== null && (int) $sourced < (int) $current);

echo 'skip='.($skip ? 'true' : 'false')."\n";
echo 'commit_type='.($major ? 'feat!' : ['minor' => 'feat', 'patch' => 'fix'][$level])."\n";
echo 'auto_merge='.($major ? 'false' : 'true')."\n";
echo "branch=regenerate/{$tag}\n";
echo 'close='.implode(' ', $close)."\n";
