<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ReturnTypeExtension\Symfony;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\PHPStanRules\TypeResolver\ClassConstFetchReturnTypeResolver;

/**
 * @inspiration https://github.com/phpstan/phpstan-symfony/blob/master/src/Type/Symfony/ServiceDynamicReturnTypeExtension.php
 *
 * @see \Symplify\PHPStanRules\Tests\ReturnTypeExtension\ContainerGetReturnTypeExtension\ContainerGetReturnTypeExtensionTest
 */
final readonly class ContainerGetReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function __construct(
        private ClassConstFetchReturnTypeResolver $classConstFetchReturnTypeResolver
    ) {
    }

    public function getClass(): string
    {
        return ContainerInterface::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'get';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): ?Type {
        return $this->classConstFetchReturnTypeResolver->resolve($methodReflection, $methodCall);
    }
}
