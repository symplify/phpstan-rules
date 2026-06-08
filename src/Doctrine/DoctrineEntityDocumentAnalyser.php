<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Doctrine;

use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\Reflection\ClassReflection;

final readonly class DoctrineEntityDocumentAnalyser
{
    /**
     * @var string[]
     */
    private const array ENTITY_DOCBLOCK_MARKERS = ['@Document', '@ORM\\Document', '@Entity', '@ORM\\Entity'];

    public static function isEntityClass(ClassReflection $classReflection): bool
    {
        $resolvedPhpDocBlock = $classReflection->getResolvedPhpDoc();
        if (! $resolvedPhpDocBlock instanceof ResolvedPhpDocBlock) {
            return false;
        }

        return array_any(self::ENTITY_DOCBLOCK_MARKERS, fn ($entityDocBlockMarkers): bool => str_contains($resolvedPhpDocBlock->getPhpDocString(), $entityDocBlockMarkers));
    }
}
