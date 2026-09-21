<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source\SomeUserHelper;

final class PromotedJugglingService
{
    public function __construct(
        private readonly SomeUserHelper $userHelper,
    ) {
    }

    public function run(): void
    {
        $this->handle($this->userHelper);
    }

    public function handle(SomeUserHelper $userHelper): void
    {
    }
}
