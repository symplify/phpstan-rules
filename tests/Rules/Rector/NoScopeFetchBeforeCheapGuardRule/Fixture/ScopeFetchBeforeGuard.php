<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoScopeFetchBeforeCheapGuardRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\PHPStan\ScopeFetcher;

final class ScopeFetchBeforeGuard
{
    public function refactor(ClassMethod $node): ?Node
    {
        $scope = ScopeFetcher::fetch($node);
        if ($node->returnType instanceof Node) {
            return null;
        }

        return $node;
    }
}
