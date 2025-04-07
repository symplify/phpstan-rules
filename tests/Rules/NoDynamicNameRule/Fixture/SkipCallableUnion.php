<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

use Closure;

final class SkipCallableUnion
{
    /** @var array|callable():array */
    private $sometimesCallable = [];

    public function run()
    {
        $sometimesCallable = $this->sometimesCallable;
        if (is_callable($sometimesCallable)) {
            $sometimesCallable();
        }
    }
}
