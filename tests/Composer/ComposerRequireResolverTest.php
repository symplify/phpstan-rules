<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Composer;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Composer\ComposerRequireResolver;

final class ComposerRequireResolverTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test(string $workingDirectory, bool $expectedHasPackage): void
    {
        $composerRequireResolver = new ComposerRequireResolver($workingDirectory);

        $this->assertSame($expectedHasPackage, $composerRequireResolver->hasRequiredPackage('symfony/console'));
    }

    /**
     * @return Iterator<array{string, bool}>
     */
    public static function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/with-require', true];
        yield [__DIR__ . '/Fixture/with-require-dev', false];
        yield [__DIR__ . '/Fixture/without', false];
        yield [__DIR__ . '/Fixture/non-existing-directory', false];
    }
}
