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
        self::assertSame(['skip' => 'false', 'commit_type' => 'feat', 'auto_merge' => 'true', 'branch' => 'regenerate/v1.1.0', 'close' => ''], $plan);
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

    public function test_a_source_tag_newer_than_the_tag_is_skipped(): void
    {
        self::assertSame('true', $this->plan('v1.1.0', 'minor', self::URL, 'v1.2.0')[1]['skip']);
    }

    public function test_a_source_tag_older_than_the_tag_is_regenerated_by_version_not_by_text(): void
    {
        $url = 'https://raw.githubusercontent.com/PuntjesApp/puntjes-openapi/v1.10.0/api.json';

        self::assertSame('false', $this->plan('v1.10.0', 'minor', $url, 'v1.9.0')[1]['skip']);
        self::assertSame('true', $this->plan('v1.9.0', 'minor', str_replace('v1.10.0', 'v1.9.0', $url), 'v1.10.0')[1]['skip']);
    }

    public function test_an_open_regenerate_pr_for_a_newer_tag_skips_the_older_tag(): void
    {
        [, $plan] = $this->plan('v1.1.0', 'minor', self::URL, 'main@abc1234', "regenerate/v1.0.0\nregenerate/v1.2.0");

        self::assertSame('true', $plan['skip']);
        self::assertSame('', $plan['close']);
    }

    public function test_the_open_regenerate_prs_for_older_tags_are_closed(): void
    {
        [, $plan] = $this->plan('v1.1.0', 'minor', self::URL, 'v1.0.0', "regenerate/v1.0.1\nregenerate/v1.1.0\nfeature/x\nregenerate/v0.9.0\nregenerate/v1.0.2;rm");

        self::assertSame('false', $plan['skip']);
        self::assertSame('regenerate/v1.0.1 regenerate/v0.9.0', $plan['close']);
    }

    public function test_closing_an_open_major_for_an_older_tag_makes_the_new_pr_a_major(): void
    {
        [, $plan] = $this->plan('v1.1.0', 'patch', self::URL, 'v1.0.0', 'regenerate/v1.0.5 feat!: regenerate from puntjes-openapi v1.0.5');

        self::assertSame('regenerate/v1.0.5', $plan['close']);
        self::assertSame('feat!', $plan['commit_type']);
        self::assertSame('false', $plan['auto_merge']);
    }

    public function test_closing_an_open_minor_for_an_older_tag_keeps_the_new_level(): void
    {
        [, $plan] = $this->plan('v1.1.0', 'patch', self::URL, 'v1.0.0', 'regenerate/v1.0.5 feat: regenerate from puntjes-openapi v1.0.5');

        self::assertSame('regenerate/v1.0.5', $plan['close']);
        self::assertSame('fix', $plan['commit_type']);
        self::assertSame('true', $plan['auto_merge']);
    }

    public function test_a_source_tag_with_a_lower_major_makes_the_new_pr_a_major(): void
    {
        $url = 'https://raw.githubusercontent.com/PuntjesApp/puntjes-openapi/v2.0.1/api.json';
        [, $plan] = $this->plan('v2.0.1', 'patch', $url, 'v1.9.0');

        self::assertSame('feat!', $plan['commit_type']);
        self::assertSame('false', $plan['auto_merge']);
    }

    public function test_a_source_that_is_not_a_tag_keeps_the_new_level(): void
    {
        [, $plan] = $this->plan('v1.1.0', 'patch', self::URL, 'main@abc1234');

        self::assertSame('fix', $plan['commit_type']);
        self::assertSame('true', $plan['auto_merge']);
    }
}
