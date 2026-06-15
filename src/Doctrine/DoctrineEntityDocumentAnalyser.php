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

    /**
     * @var string[]
     */
    private const ENTITY_ATTRIBUTES = [
        'Doctrine\\ORM\\Mapping\\Entity',
        'Doctrine\\ODM\\MongoDB\\Mapping\\Annotations\\Document',
    ];

    public static function isEntityClass(ClassReflection $classReflection): bool
    {
        if (self::hasEntityAttribute($classReflection)) {
            return true;
        }

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

    private static function hasEntityAttribute(ClassReflection $classReflection): bool
    {
        $attributeReflections = method_exists($classReflection->getNativeReflection(), 'getAttributes') ? $classReflection->getNativeReflection()
            ->getAttributes() : [];
        $found = false;
        foreach ($attributeReflections as $reflectionAttribute) {
            if (in_array(
                $reflectionAttribute->getName(),
                self::ENTITY_ATTRIBUTES,
                true
            )) {
                $found = true;
                break;
            }
        }
        return $found;
    }
}
