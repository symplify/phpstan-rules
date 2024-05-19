<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\MethodCallableNode;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Node\MethodCallableNode;
use Symplify\PHPStanRules\Matcher\ClassMethodCallReferenceResolver;
use Symplify\PHPStanRules\ValueObject\MethodCallReference;

/**
 * @implements Collector<MethodCallableNode, array<string>|null>
 *
 * PHPStan has special node for first class callables of MethodCall
 *
 * @see https://github.com/phpstan/phpstan-src/blob/511c1e435fb43b8eb0ac310e6aa3230147963790/src/Analyser/NodeScopeResolver.php#L1936
 */
final class MethodCallableCollector implements Collector
{
    /**
     * @readonly
     * @var \Symplify\PHPStanRules\Matcher\ClassMethodCallReferenceResolver
     */
    private $classMethodCallReferenceResolver;
    public function __construct(ClassMethodCallReferenceResolver $classMethodCallReferenceResolver)
    {
        $this->classMethodCallReferenceResolver = $classMethodCallReferenceResolver;
    }
    public function getNodeType(): string
    {
        return MethodCallableNode::class;
    }

    /**
     * @param MethodCallableNode $node
     * @return array{string}|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        $classMethodCallReference = $this->classMethodCallReferenceResolver->resolve($node, $scope, true);
        if (! $classMethodCallReference instanceof MethodCallReference) {
            return null;
        }

        $classMethodReference = $this->createClassMethodReference($classMethodCallReference);

        // special case that should skip everything
        return [$classMethodReference];
    }

    private function createClassMethodReference(MethodCallReference $classMethodCallReference): string
    {
        $className = $classMethodCallReference->getClass();
        $methodName = $classMethodCallReference->getMethod();

        return $className . '::' . $methodName;
    }
}
