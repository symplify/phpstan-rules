<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ClassReflection;

/**
 * Collects every "static::SOME_CONST" fetch, so a child class overriding that constant can later be looked up.
 *
 * @implements Collector<ClassConstFetch, array{string, string, int}>
 */
final class StaticClassConstFetchCollector implements Collector
{
    public function getNodeType(): string
    {
        return ClassConstFetch::class;
    }

    /**
     * @return array{string, string, int}|null the class the fetch lives in, the constant name and its line
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (! $node->class instanceof Name) {
            return null;
        }

        if ($node->class->toString() !== 'static') {
            return null;
        }

        // "static::class" is late static binding magic, not an overridable constant
        if (! $node->name instanceof Identifier || $node->name->toLowerString() === 'class') {
            return null;
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        // abstract class is designed to be extended, so static fetch is expected
        if ($classReflection->isAbstract()) {
            return null;
        }

        return [$classReflection->getName(), $node->name->toString(), $node->getStartLine()];
    }
}
