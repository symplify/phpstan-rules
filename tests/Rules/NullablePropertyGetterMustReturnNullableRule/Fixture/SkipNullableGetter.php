<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NullablePropertyGetterMustReturnNullableRule\Fixture;

final class SkipNullableGetter
{
    private ?string $name = null;

    private string $title = '';

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
