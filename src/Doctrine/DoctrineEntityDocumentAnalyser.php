<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Doctrine;

use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\Reflection\ClassReflection;

final class DoctrineEntityDocumentAnalyser
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
        $found = false;
        foreach (self::ENTITY_DOCBLOCK_MARKERS as $entityDocBlockMarker) {
            if (strpos($resolvedPhpDocBlock->getPhpDocString(), $entityDocBlockMarker) !== false) {
                $found = true;
                break;
            }
        }
        return $found;
    }
}
