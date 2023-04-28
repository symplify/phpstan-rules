<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule\Fixture;

final class FluentSetterClass
{
    public function setName(string $name)
    {
        return $this;
    }
}
