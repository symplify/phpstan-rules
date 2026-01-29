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
        foreach (self::TEST_FILE_SUFFIXES as $testFileSuffix) {
            if (str_ends_with($scope->getFile(), $testFileSuffix)) {
                return true;
            }
        }

        return false;
    }
}
