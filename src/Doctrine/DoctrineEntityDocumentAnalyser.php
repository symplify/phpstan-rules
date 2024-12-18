<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\PHPStan;

use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\Reflection\ClassReflection;

final readonly class DoctrineEntityDocumentAnalyser
{
    /**
     * @var string[]
     */
    private const ENTITY_DOCBLOCK_MARKERS = ['@Document', '@ORM\\Document', '@Entity', '@ORM\\Entity'];

    public static function isEntityClass(ClassReflection $classReflection): bool
    {
        $resolvedPhpDocBlock = $classReflection->getResolvedPhpDoc();
        if (! $resolvedPhpDocBlock instanceof ResolvedPhpDocBlock) {
            return false;
        }

        foreach (self::ENTITY_DOCBLOCK_MARKERS as $entityDocBlockMarkers) {
            if (str_contains($resolvedPhpDocBlock->getPhpDocString(), $entityDocBlockMarkers)) {
                return true;
            }
        }

        return false;
    }
}
