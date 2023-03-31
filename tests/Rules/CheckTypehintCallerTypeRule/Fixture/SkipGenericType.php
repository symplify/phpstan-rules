<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;

/**
 * @template T of MethodCall
 */
class SkipGenericType
{
    /**
     * @param T $node
     * @return void
     */
    public function run(Node $node)
    {
        $this->getsAGeneric($node);
    }

    /**
     * @param T $node
     * @return void
     */
    private function getsAGeneric(Node $node)
    {
    }
}
