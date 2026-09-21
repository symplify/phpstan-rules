<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\SomeService;

final class SkipAnonymousClass
{
    public function create(): object
    {
        return new class(null) {
            public function __construct(
                private readonly ?SomeService $someService,
            ) {
            }
        };
    }
}
