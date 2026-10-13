<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NullablePropertyGetterMustReturnNullableRule\Fixture;

final class ReportNullableGetter
{
    private ?string $name = null;

    public function getName(): string
    {
        return $this->name;
    }
}
