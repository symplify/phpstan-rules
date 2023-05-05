<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Naming;

use Nette\Utils\Strings;

/**
 * @see \Symplify\PHPStanRules\Tests\Naming\ClassToSuffixResolverTest
 */
final class ClassToSuffixResolver
{
    public function resolveFromClass(string $parentClass): string
    {
        $expectedSuffix = strpos($parentClass, '\\') !== false ? (string) Strings::after(
            $parentClass,
            '\\',
            -1
        ) : $parentClass;

        $expectedSuffix = $this->removeAbstractInterfacePrefixSuffix($expectedSuffix);

        // special case for tests
        if ($expectedSuffix === 'TestCase') {
            return 'Test';
        }

        return $expectedSuffix;
    }

    private function removeAbstractInterfacePrefixSuffix(string $parentType): string
    {
        if (substr_compare($parentType, 'Interface', -strlen('Interface')) === 0) {
            $parentType = Strings::substring($parentType, 0, -strlen('Interface'));
        }

        if (substr_compare($parentType, 'Abstract', -strlen('Abstract')) === 0) {
            $parentType = Strings::substring($parentType, 0, -strlen('Abstract'));
        }

        if (strncmp($parentType, 'Abstract', strlen('Abstract')) === 0) {
            return Strings::substring($parentType, strlen('Abstract'));
        }

        return $parentType;
    }
}
