<?php

declare(strict_types=1);

namespace Rules\CheckTypehintCallerTypeRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;

class SkipPhpdoc
{
    /** @param MethodCall $node */
    public function run($node)
    {
        if (rand(0,1)) {
            $this->isCheck($node);
        }
    }

    private function isCheck(Node $node)
    {
    }
}
