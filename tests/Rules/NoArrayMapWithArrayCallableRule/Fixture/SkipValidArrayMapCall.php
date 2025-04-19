<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayMapWithArrayCallableRule\Fixture;

final class SkipValidArrayMapCall
{
    public function run(array $items)
    {
        $changedItems = array_map(fn($item) => $this->change($item), $items);
    }

    public function change($item)
    {
        // ...
    }
}
