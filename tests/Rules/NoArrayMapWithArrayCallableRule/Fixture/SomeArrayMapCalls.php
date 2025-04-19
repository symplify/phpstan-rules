<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayMapWithArrayCallableRule\Fixture;

final class SomeArrayMapCalls
{
    public function run(array $items)
    {
        $changedItems = array_map([$this, 'change'], $items);
    }

    public function change()
    {
        // ...
    }
}
