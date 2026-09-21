<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source;

final class SomeUserHelper
{
    public function getUser(): ?string
    {
        return null;
    }
}
