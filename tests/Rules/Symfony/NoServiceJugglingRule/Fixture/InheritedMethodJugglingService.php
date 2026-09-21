<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source\ParentJugglingService;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source\SomeUserHelper;

final class InheritedMethodJugglingService extends ParentJugglingService
{
    public function __construct(
        private readonly SomeUserHelper $userHelper,
    ) {
    }

    public function run(): void
    {
        // the method is shared by every child, so the service is an argument of the call, not a dependency
        $this->handle($this->userHelper);
    }
}
