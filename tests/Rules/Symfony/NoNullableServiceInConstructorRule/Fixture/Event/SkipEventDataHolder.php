<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture\Event;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\SomeService;

final class SkipEventDataHolder
{
    public function __construct(
        private readonly ?SomeService $someService,
    ) {
    }
}
