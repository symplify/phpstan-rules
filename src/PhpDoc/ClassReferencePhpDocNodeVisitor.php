<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use Nette\Utils\Reflection;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\PHPStanRules\PhpDocParser\PhpDocNodeVisitor\AbstractPhpDocNodeVisitor;

final class ClassReferencePhpDocNodeVisitor extends AbstractPhpDocNodeVisitor
{
    private ?string $className = null;

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider
    ) {
    }

    public function configureClassName(string $className): void
    {
        $this->className = $className;
    }

    public function enterNode(Node $node): Node
    {
        if ($node instanceof PhpDocTagNode) {
            $this->processPhpDocTagNode($node);
        }

        return $node;
    }

    private function resolveShortNamesToFullyQualified(string $currentName, string $className): ?string
    {
        if (! class_exists($className)) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        return Reflection::expandClassName($currentName, $classReflection->getNativeReflection());
    }

    private function processPhpDocTagNode(PhpDocTagNode $phpDocTagNode): void
    {
        if ($this->className === null) {
            throw new ShouldNotHappenException();
        }

        $shortClassName = trim($phpDocTagNode->name, '@');

        // lowercased, probably non class annotation
        if (strtolower($shortClassName) === $shortClassName) {
            return;
        }

        $resolvedFullyQualifiedName = $this->resolveShortNamesToFullyQualified($shortClassName, $this->className);
        if ($resolvedFullyQualifiedName) {
            $phpDocTagNode->name = $resolvedFullyQualifiedName;
        }
    }
}
