<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\SomeService;

final class SkipPublicPropertyDataHolder
{
    public function __construct(
        public readonly ?SomeService $price,
    ) {
    }
}
