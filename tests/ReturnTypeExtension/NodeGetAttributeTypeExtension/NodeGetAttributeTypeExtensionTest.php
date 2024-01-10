<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\ReturnTypeExtension\NodeGetAttributeTypeExtension;

use Iterator;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @see \Symplify\PHPStanRules\ReturnTypeExtension\NodeGetAttributeTypeExtension
 */
final class NodeGetAttributeTypeExtensionTest extends TypeInferenceTestCase
{
    #[DataProvider('dataAsserts')]
    public function testAsserts(string $assertType, string $file, mixed ...$args): void
    {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    public static function dataAsserts(): Iterator
    {
        yield from self::gatherAssertTypes(__DIR__ . '/data/get_parent_node.php.inc');
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/type_extension.neon'];
    }
}
