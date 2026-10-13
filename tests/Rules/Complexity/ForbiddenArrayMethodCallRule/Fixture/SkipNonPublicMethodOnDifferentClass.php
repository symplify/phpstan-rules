<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenArrayMethodCallRule\Fixture;

final class SkipNonPublicMethodOnDifferentClass
{
    public function run(SomeHandlerWithProtectedMethod $someHandler)
    {
        return [$someHandler, 'resolveKey'];
    }
}
