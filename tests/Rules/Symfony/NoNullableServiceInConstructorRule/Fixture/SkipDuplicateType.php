<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\SomeService;

final class SkipDuplicateType
{
    public function __construct(
        private readonly SomeService $primary,
        private readonly ?SomeService $secondary,
    ) {
    }
}
