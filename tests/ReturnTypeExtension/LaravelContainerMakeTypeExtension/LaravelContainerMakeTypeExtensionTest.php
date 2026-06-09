<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\ReturnTypeExtension\LaravelContainerMakeTypeExtension;

use Iterator;
use Override;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @see \Symplify\PHPStanRules\ReturnTypeExtension\Laravel\LaravelContainerMakeTypeExtension
 */
final class LaravelContainerMakeTypeExtensionTest extends TypeInferenceTestCase
{
    #[DataProvider('dataAsserts')]
    public function testAsserts(string $assertType, string $file, mixed ...$args): void
    {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    /**
     * @return Iterator<array<string, mixed>>
     */
    public static function dataAsserts(): Iterator
    {
        yield from self::gatherAssertTypes(__DIR__ . '/data/container_make.php.inc');
    }

    /**
     * @return string[]
     */
    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/type_extension.neon'];
    }
}
