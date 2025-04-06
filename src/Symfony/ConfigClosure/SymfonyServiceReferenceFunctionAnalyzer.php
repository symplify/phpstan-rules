<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ConfigClosure;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Symplify\PHPStanRules\Enum\SymfonyFunctionName;

final class SymfonyServiceReferenceFunctionAnalyzer
{
    public static function isReferenceCall(Expr $expr): bool
    {
        if (! $expr instanceof FuncCall) {
            return false;
        }

        if (! $expr->name instanceof Name) {
            return false;
        }

        $functionName = $expr->name->toString();

        return in_array($functionName, [
            SymfonyFunctionName::REF,
            SymfonyFunctionName::SERVICE,
        ]);
    }
}
