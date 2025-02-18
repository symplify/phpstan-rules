<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoJustPropertyAssignRule\Fixture;

use ArrayIterator;
use Iterator;

final class SkipSpecificVarDoc
{
    public Iterator $it;

    public function __construct()
    {
        $this->it = new ArrayIterator([]);
    }

    public function run()
    {
        /**
         * @var ArrayIterator $it
         */
        $it = $this->it;
    }
}
