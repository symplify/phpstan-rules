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
    /**
     * @readonly
     * @var \Symplify\PHPStanRules\Reflection\ReflectionParser
     */
    private $reflectionParser;
    /**
     * @readonly
     * @var \PhpParser\NodeFinder
     */
    private $nodeFinder;
    /**
     * @readonly
     * @var \Symplify\PHPStanRules\Printer\NodeComparator
     */
    private $nodeComparator;
    public function __construct(ReflectionParser $reflectionParser, NodeFinder $nodeFinder, NodeComparator $nodeComparator)
    {
        $this->reflectionParser = $reflectionParser;
        $this->nodeFinder = $nodeFinder;
        $this->nodeComparator = $nodeComparator;
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
