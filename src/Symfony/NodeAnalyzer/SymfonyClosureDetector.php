<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer;

use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Name;
use Symplify\PHPStanRules\Enum\SymfonyClass;

final class SymfonyClosureDetector
{
    public static function detect(Closure $closure): bool
    {
        if (count($closure->getParams()) !== 1) {
            return false;
        }

        $onlyParam = $closure->getParams()[0];
        if (! $onlyParam->type instanceof Name) {
            return false;
        }

        $parameterName = $onlyParam->type->toString();
        return $parameterName === SymfonyClass::CONTAINER_CONFIGURATOR;
    }
}
