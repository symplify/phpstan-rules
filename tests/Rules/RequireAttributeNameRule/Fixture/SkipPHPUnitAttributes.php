<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireAttributeNameRule\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;

final class SkipPHPUnitAttributes
{
    #[DataProvider('some_method')]
    public function action()
    {
    }
}
