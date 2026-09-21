<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * Collects the config-closure aliases that point a string service id at a class,
 * e.g. $services->alias('some.helper', SomeHelper::class).
 *
 * Such an id names the very same service its class name does, so a reference by the class name says the same
 * without the loose string. The alias the other way around, $services->alias(SomeHelper::class, 'some.helper'),
 * is left out.
 *
 * @implements Collector<MethodCall, array{string, string, int}>
 */
final class ClassTargetServiceAliasCollector implements Collector
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @return array{string, string, int}|null the string service id, the class name it points at and the
     *                                          line of the alias() call
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (! $node->name instanceof Identifier || $node->name->toString() !== 'alias') {
            return null;
        }

        $args = $node->getArgs();
        if (count($args) !== 2) {
            return null;
        }

        if (! $args[0]->value instanceof String_) {
            return null;
        }

        $className = $this->matchClassName($args[1]->value);
        if ($className === null) {
            return null;
        }

        return [$args[0]->value->value, $className, $node->getStartLine()];
    }

    private function matchClassName(Node $aliasValue): ?string
    {
        if (! $aliasValue instanceof ClassConstFetch || ! $aliasValue->class instanceof Name) {
            return null;
        }

        if (! $aliasValue->name instanceof Identifier || $aliasValue->name->toLowerString() !== 'class') {
            return null;
        }

        return $aliasValue->class->toString();
    }
}
