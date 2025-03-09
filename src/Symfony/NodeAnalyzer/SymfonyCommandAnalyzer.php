<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer;

use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Enum\SymfonyClass;

final class SymfonyCommandAnalyzer
{
    public static function isCommandScope(Scope $scope): bool
    {
        if (! $scope->isInClass()) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        return $classReflection->is(SymfonyClass::COMMAND);
    }
}
