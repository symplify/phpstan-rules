<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * Collects each class with its parent and declared constant names, so a child class overriding a parent
 * constant can be detected.
 *
 * @implements Collector<Class_, array{string, string|null, string[]}>
 */
final class ClassConstantDeclarationCollector implements Collector
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @return array{string, string|null, string[]}|null the class name, its parent class name and the
     *                                                    constant names it declares
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (! $node->namespacedName instanceof Name) {
            return null;
        }

        $parentClassName = $node->extends instanceof Name ? $node->extends->toString() : null;

        $constantNames = [];
        foreach ($node->getConstants() as $classConst) {
            foreach ($classConst->consts as $const) {
                $constantNames[] = $const->name->toString();
            }
        }

        return [$node->namespacedName->toString(), $parentClassName, $constantNames];
    }
}
