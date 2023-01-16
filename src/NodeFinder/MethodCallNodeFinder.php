<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\PHPStanRules\Reflection\ReflectionParser;

final class MethodCallNodeFinder
{
    public function __construct(
        private readonly ReflectionParser $reflectionParser,
        private readonly NodeFinder $nodeFinder,
        private readonly NodeComparator $nodeComparator,
    ) {
    }

    /**
     * @return MethodCall[]
     */
    public function findUsages(MethodCall $methodCall, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $classLike = $this->reflectionParser->parseClassReflection($classReflection);
        if (! $classLike instanceof Class_) {
            return [];
        }

        return $this->nodeFinder->find($classLike, function (Node $node) use ($methodCall): bool {
            if (! $node instanceof MethodCall) {
                return false;
            }

            if (! $this->nodeComparator->areNodesEqual($node->var, $methodCall->var)) {
                return false;
            }

            return $this->nodeComparator->areNodesEqual($node->name, $methodCall->name);
        });
    }
}
