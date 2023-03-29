<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Fixture;

final class SkipCustomFunctionCalls
{
    public function run()
    {
        my_custom_function($this);
    }
}

function my_custom_function(object $object): void
{
}
