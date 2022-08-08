<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Matcher;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\TypeAnalyzer\ContainsTypeAnalyser;

final class PositionMatcher
{
    /**
     * @var \Symplify\PHPStanRules\TypeAnalyzer\ContainsTypeAnalyser
     */
    private $containsTypeAnalyser;
    public function __construct(ContainsTypeAnalyser $containsTypeAnalyser)
    {
        $this->containsTypeAnalyser = $containsTypeAnalyser;
    }

    /**
     * @param class-string $desiredType
     * @param array<string, int[]> $positionsByMethods
     * @return int[]|null
     */
    public function matchPositions(
        MethodCall $methodCall,
        Scope $scope,
        string $desiredType,
        array $positionsByMethods,
        string $methodName
    ): ?array {
        if (! $this->containsTypeAnalyser->containsExprType($methodCall->var, $scope, $desiredType)) {
            return null;
        }

        return $positionsByMethods[$methodName] ?? null;
    }
}
