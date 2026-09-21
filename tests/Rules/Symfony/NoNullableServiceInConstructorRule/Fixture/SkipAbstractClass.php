<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\SomeService;

abstract class SkipAbstractClass
{
    public function __construct(
        protected readonly ?SomeService $someService,
    ) {
    }
}
