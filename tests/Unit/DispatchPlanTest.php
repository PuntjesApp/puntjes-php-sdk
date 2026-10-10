<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class DispatchPlanTest extends TestCase
{
    private const URL = 'https://raw.githubusercontent.com/PuntjesApp/puntjes-openapi/v1.1.0/api.json';

    /** @return array{0: int, 1: array<string, string>} */
    private function plan(string ...$args): array
    {
        exec('php '.escapeshellarg(__DIR__.'/../../scripts/dispatch-plan.php').' '.implode(' ', array_map('escapeshellarg', $args)).' 2>&1', $out, $exit);
        $pairs = [];
        foreach ($out as $line) {
            [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
            $pairs[$k] = $v;
        }

        return [$exit, $pairs];
    }

    public function test_a_minor_is_a_feat_that_merges_itself(): void
    {
        [$exit, $plan] = $this->plan('v1.1.0', 'minor', self::URL, 'main@abc1234');

        self::assertSame(0, $exit);
        self::assertSame(['skip' => 'false', 'commit_type' => 'feat', 'auto_merge' => 'true', 'branch' => 'regenerate/v1.1.0'], $plan);
    }

    public function test_a_major_is_breaking_and_waits_for_a_person(): void
    {
        [, $plan] = $this->plan('v1.1.0', 'major', self::URL, 'main@abc1234');

        self::assertSame('feat!', $plan['commit_type']);
        self::assertSame('false', $plan['auto_merge']);
    }

    public function test_a_patch_is_a_fix(): void
    {
        self::assertSame('fix', $this->plan('v1.1.0', 'patch', self::URL, 'main@abc1234')[1]['commit_type']);
    }

    public function test_the_tag_already_in_source_is_skipped(): void
    {
        self::assertSame('true', $this->plan('v1.1.0', 'minor', self::URL, 'v1.1.0')[1]['skip']);
    }

    public function test_a_url_outside_puntjes_openapi_is_refused(): void
    {
        self::assertSame(1, $this->plan('v1.1.0', 'minor', 'https://example.com/api.json', 'x')[0]);
        self::assertSame(1, $this->plan('v1.1.0', 'minor', 'https://raw.githubusercontent.com/PuntjesApp/puntjes-openapi/v9.9.9/api.json', 'x')[0]);
    }

    public function test_an_unknown_level_or_tag_is_refused(): void
    {
        self::assertSame(1, $this->plan('v1.1.0', 'huge', self::URL, 'x')[0]);
        self::assertSame(1, $this->plan('v1.1.0;rm', 'minor', self::URL, 'x')[0]);
    }
}
