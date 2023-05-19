<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ValueObject;

use PHPStan\BetterReflection\Reflection\Adapter\ReflectionProperty;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use PHPStan\Type\Type;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class PropertyMetadata
{
    /**
     * @readonly
     * @var \PHPStan\Reflection\Php\PhpPropertyReflection
     */
    private $phpPropertyReflection;
    /**
     * @readonly
     * @var \PHPStan\BetterReflection\Reflection\Adapter\ReflectionProperty
     */
    private $nativeReflectionProperty;
    /**
     * @readonly
     * @var int
     */
    private $propertyLine;
    public function __construct(PhpPropertyReflection $phpPropertyReflection, ReflectionProperty $nativeReflectionProperty, int $propertyLine)
    {
        $this->phpPropertyReflection = $phpPropertyReflection;
        $this->nativeReflectionProperty = $nativeReflectionProperty;
        $this->propertyLine = $propertyLine;
    }

    public function getPropertyType(): Type
    {
        return $this->phpPropertyReflection->getReadableType();
    }

    public function getDocComment(): string
    {
        return (string) $this->phpPropertyReflection->getDocComment();
    }

    public function getFileName(): string
    {
        $reflectionClass = $this->nativeReflectionProperty->getDeclaringClass();

        $fileName = $reflectionClass->getFileName();
        if ($fileName === false) {
            throw new ShouldNotHappenException();
        }

        return $fileName;
    }

    public function getPropertyName(): string
    {
        return $this->nativeReflectionProperty->getName();
    }

    public function getPropertyLine(): int
    {
        return $this->propertyLine;
    }
}
