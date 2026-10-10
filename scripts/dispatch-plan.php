<?php

declare(strict_types=1);

[$self, $tag, $level, $url, $source] = $argv + [null, '', '', '', ''];

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

echo 'skip='.(trim((string) $source) === $tag ? 'true' : 'false')."\n";
echo "commit_type={$type}\n";
echo 'auto_merge='.($level === 'major' ? 'false' : 'true')."\n";
echo "branch=regenerate/{$tag}\n";
