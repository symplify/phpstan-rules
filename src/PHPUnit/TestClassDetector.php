<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PHPUnit;

use PHPStan\Analyser\Scope;

final class TestClassDetector
{
    /**
     * @var string[]
     */
    private const array TEST_FILE_SUFFIXES = [
        'Test.php',
        'TestCase.php',
        'Context.php',
    ];

    public static function isTestClass(Scope $scope): bool
    {
        return array_any(self::TEST_FILE_SUFFIXES, fn (string $testFileSuffix): bool => str_ends_with($scope->getFile(), $testFileSuffix));
    }
}
