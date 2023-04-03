<?php

namespace SkipAbstractBase;

use Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Source\ConceptBase;
use Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Source\ConceptImpl1;

class MyService {
    public function run(ConceptImpl1 $arg)
    {
        $this->isCheck($arg);
    }
    private function isCheck(ConceptBase $arg)
    {
    }
}
