<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\ErrorFormatter;

use Iterator;
use Override;
use PHPStan\Testing\ErrorFormatterTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\PHPStanRules\ErrorFormatter\SymplifyErrorFormatter;

/**
 * @see \Symplify\PHPStanRules\ErrorFormatter\SymplifyErrorFormatter
 */
final class SymplifyErrorFormatterTest extends ErrorFormatterTestCase
{
    #[DataProvider('provideData')]
    public function testFormatErrors(
        string $message,
        int $expectedExitCode,
        int $numFileErrors,
        int $numGenericErrors,
        string $expectedOutputFile,
    ): void {
        $symplifyErrorFormatter = self::getContainer()->getByType(SymplifyErrorFormatter::class);

        $analysisResult = $this->getAnalysisResult($numFileErrors, $numGenericErrors);
        $resultCode = $symplifyErrorFormatter->formatErrors($analysisResult, $this->getOutput());

        $this->assertSame($expectedExitCode, $resultCode);

        $this->assertStringMatchesFormatFile($expectedOutputFile, $this->getOutputContent());
    }

    /**
     * @return Iterator<mixed>
     */
    public static function provideData(): Iterator
    {
        yield ['Some message', 1, 1, 1, __DIR__ . '/Fixture/expected_single_message_many_files_report.txt'];
    }

    /**
     * @return string[]
     */
    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../../config/phpstan-extensions.neon'];
    }
}
