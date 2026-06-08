<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ReturnTypeExtension;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

/**
 * @see \Symplify\PHPStanRules\Tests\ReturnTypeExtension\NativeFunctionReturnTypeExtension\NativeFunctionReturnTypeExtensionTest
 */
final class NativeFunctionReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return in_array($functionReflection->getName(), ['getcwd', 'dirname', 'realpath'], true);
    }

    public function getTypeFromFunctionCall(
        FunctionReflection $functionReflection,
        FuncCall $funcCall,
        Scope $scope
    ): Type {
        return new StringType();
    }
}
