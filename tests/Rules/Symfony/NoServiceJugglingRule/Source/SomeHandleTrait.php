<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source;

trait SomeHandleTrait
{
    public function handleInTrait(SomeUserHelper $userHelper): void
    {
    }
}
