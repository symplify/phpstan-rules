<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule\Fixture;

class SkipStaticReturnSetter
{
    private $name;

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
