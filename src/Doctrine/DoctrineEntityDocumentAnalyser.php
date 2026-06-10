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

    /**
     * @var string[]
     */
    private const array ENTITY_ATTRIBUTES = [
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

        return array_any(self::ENTITY_DOCBLOCK_MARKERS, fn (string $entityDocBlockMarker): bool => str_contains($resolvedPhpDocBlock->getPhpDocString(), $entityDocBlockMarker));
    }

    private static function hasEntityAttribute(ClassReflection $classReflection): bool
    {
        $attributeReflections = $classReflection->getNativeReflection()
            ->getAttributes();

        return array_any(
            $attributeReflections,
            static fn ($reflectionAttribute): bool => in_array(
                $reflectionAttribute->getName(),
                self::ENTITY_ATTRIBUTES,
                true
            )
        );
    }
}
