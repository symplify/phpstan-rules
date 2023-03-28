<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Fixture;

final class SkipNativeFunctionCalls
{
    public function run()
    {
        if (method_exists($this, 'run')) {
            return true;
        }

        if (is_a($this, 'class')) {
            return true;
        }

        return false;
    }
}
