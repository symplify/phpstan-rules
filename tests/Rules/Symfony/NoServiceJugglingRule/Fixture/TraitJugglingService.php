<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source\SomeHandleTrait;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source\SomeUserHelper;

final class TraitJugglingService
{
    use SomeHandleTrait;

    public function __construct(
        private readonly SomeUserHelper $userHelper,
    ) {
    }

    public function run(): void
    {
        // the trait has no constructor of its own, so the service can only travel as a parameter
        $this->handleInTrait($this->userHelper);
    }
}
