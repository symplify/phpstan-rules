<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenArrayMethodCallRule\Fixture;

final class SkipNonPublicMethod
{
    public function run()
    {
        return [$this, 'resolveKey'];
    }

    protected function resolveKey()
    {
    }
}
