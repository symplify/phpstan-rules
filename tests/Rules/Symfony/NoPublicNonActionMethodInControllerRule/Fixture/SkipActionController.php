<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoPublicNonActionMethodInControllerRule\Fixture;

use Symfony\Contracts\Service\Attribute\Required;

final class SkipActionController
{
    public function listAction(): void
    {
    }

    public function __invoke(): void
    {
    }

    #[Required]
    public function setLogger(): void
    {
    }

    protected function helper(): void
    {
    }
}
