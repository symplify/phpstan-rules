<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source\ParentJugglingService;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoServiceJugglingRule\Source\SomeUserHelper;

final class AutowiredJugglingService extends ParentJugglingService
{
    private SomeUserHelper $userHelper;

    public function autowireAutowiredJugglingService(SomeUserHelper $userHelper): void
    {
        $this->userHelper = $userHelper;
    }

    public function run(): void
    {
        parent::handle($this->userHelper);
    }
}
