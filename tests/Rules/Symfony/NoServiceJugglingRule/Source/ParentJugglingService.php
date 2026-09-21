<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source;

abstract class ParentJugglingService
{
    public function handle(SomeUserHelper $userHelper): void
    {
    }
}
