<?php

declare(strict_types=1);

namespace Puntjes\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class GeneratedCodeTest extends TestCase
{
    public function test_every_file_with_a_query_parameter_annotation_imports_it(): void
    {
        $files = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__.'/../../src/Generated')) as $file) {
            if (str_ends_with($file->getFilename(), 'QueryParameters.php')) {
                $files[] = $file->getPathname();
            }
        }
        $annotated = array_filter($files, fn (string $f): bool => str_contains((string) file_get_contents($f), '@QueryParameter('));

        self::assertNotEmpty($annotated);

        foreach ($annotated as $file) {
            self::assertStringContainsString("use Microsoft\\Kiota\\Abstractions\\QueryParameter;", (string) file_get_contents($file), $file);
        }
    }

    public function test_the_client_registers_only_the_json_and_text_formats(): void
    {
        $source = (string) file_get_contents(__DIR__.'/../../src/Generated/PuntjesClient.php');

        self::assertStringContainsString('JsonParseNodeFactory', $source);
        self::assertStringNotContainsString('FormParseNodeFactory', $source);
        self::assertStringNotContainsString('Multipart', $source);
    }
}
