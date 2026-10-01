<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\SomeEnum;

final class SkipNullableEnum
{
    public function __construct(
        private readonly ?SomeEnum $someEnum,
    ) {
    }
}
