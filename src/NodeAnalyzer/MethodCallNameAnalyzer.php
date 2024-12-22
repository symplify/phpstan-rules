<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;

final class MethodCallNameAnalyzer
{
    public static function isThisMethodCall(MethodCall $methodCall, string $methodName): bool
    {
        if (! $methodCall->name instanceof Identifier) {
            return false;
        }

        if ($methodCall->name->toString() !== $methodName) {
            return false;
        }

        // is "$this"?
        if (! $methodCall->var instanceof Variable) {
            return false;
        }

        if (! is_string($methodCall->var->name)) {
            return false;
        }

        return $methodCall->var->name === 'this';
    }
}
