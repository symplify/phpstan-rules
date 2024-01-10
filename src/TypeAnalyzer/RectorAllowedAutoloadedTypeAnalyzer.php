<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Node;
use PHPStan\PhpDocParser\Ast\Node as PhpDocNode;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\Generic\GenericClassStringType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

final class RectorAllowedAutoloadedTypeAnalyzer
{
    /**
     * @see https://regex101.com/r/BBm9bf/1
     * @var string
     */
    private const AUTOLOADED_CLASS_PREFIX_REGEX = '#^(PhpParser|PHPStan|Rector|Reflection|Symfony\\\\Component\\\\Console)#';

    /**
     * @var array<string>
     */
    private const ALLOWED_CLASSES = [
        Node::class,
        PhpDocNode::class,
    ];

    public static function isAllowedType(Type $type): bool
    {
        if ($type instanceof UnionType) {
            foreach ($type->getTypes() as $unionedType) {
                if (! self::isAllowedType($unionedType)) {
                    return false;
                }
            }

            return true;
        }

        if ($type instanceof ConstantStringType) {
            return self::isAllowedClassString($type->getValue());
        }

        if ($type instanceof ObjectType) {
            return self::isAllowedClassString($type->getClassName());
        }

        if ($type instanceof GenericClassStringType) {
            return self::isAllowedType($type->getGenericType());
        }

        return false;
    }

    private static function isAllowedClassString(string $value): bool
    {
        // autoloaded allowed type
        if (Strings::match($value, self::AUTOLOADED_CLASS_PREFIX_REGEX) !== null) {
            return true;
        }

        foreach (self::ALLOWED_CLASSES as $allowedClass) {
            if ($value === $allowedClass) {
                return true;
            }

            if (is_a($value, $allowedClass, true)) {
                return true;
            }
        }

        return false;
    }
}
