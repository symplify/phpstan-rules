<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source\SomeUserHelper;

final class VaryingArgumentJugglingService
{
    public function __construct(
        private readonly SomeUserHelper $userHelper,
    ) {
    }

    // the same position is filled differently, so it is a parameter of its own
    public function run(SomeUserHelper $otherUserHelper): void
    {
        $this->handle($this->userHelper);
        $this->handle($otherUserHelper);
    }

    public function handle(SomeUserHelper $userHelper): void
    {
    }
}
