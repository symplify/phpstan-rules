<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoJustPropertyAssignRule\Fixture;

use Iterator;

final class WithEqualTypeVarDoc
{
    public Iterator $it;

    public function run()
    {
        /**
         * @var Iterator $it
         */
        $it = $this->it;
    }
}
