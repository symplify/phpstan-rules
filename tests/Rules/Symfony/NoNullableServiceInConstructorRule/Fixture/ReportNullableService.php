<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\AnotherService;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\SomeService;

final class ReportNullableService
{
    public function __construct(
        private readonly ?SomeService $someService,
        private readonly AnotherService|null $anotherService,
    ) {
    }
}
