<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PHPUnit;

use PHPStan\Analyser\Scope;

final class TestClassDetector
{
    /**
     * @var string[]
     */
    private const TEST_FILE_SUFFIXES = [
        'Test.php',
        'TestCase.php',
        'Context.php',
    ];

    public static function isTestClass(Scope $scope): bool
    {
        foreach (self::TEST_FILE_SUFFIXES as $testFileSuffix) {
            if (substr_compare($scope->getFile(), $testFileSuffix, -strlen($testFileSuffix)) === 0) {
                return true;
            }
        }

        return false;
    }
}
