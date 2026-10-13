<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoPublicNonActionMethodInControllerRule\Fixture;

final class SomeController
{
    public function helper()
    {
    }
}
