<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule\Fixture;

final class SkipSelfReturnSetter
{
    private $name;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
