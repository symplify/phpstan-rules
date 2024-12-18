<?php

declare(strict_types=1);

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;

// node name/value checks

function fast_node_named(Node $node, string $desiredName): bool
{
    if ($node instanceof Identifier || $node instanceof Name) {
        return $node->toString() === $desiredName;
    }

    return false;
}

// reflections

function fast_has_parent_constructor(Scope $scope): bool
{
    $classReflection = $scope->getClassReflection();
    if (! $classReflection instanceof ClassReflection) {
        return false;
    }

    // anonymous class? let it go
    if ($classReflection->isAnonymous()) {
        return false;
    }

    $parentClassReflection = $classReflection->getParentClass();

    // no parent class? let it go
    if (! $parentClassReflection instanceof ClassReflection) {
        return false;
    }

    return $parentClassReflection->hasConstructor();
}
