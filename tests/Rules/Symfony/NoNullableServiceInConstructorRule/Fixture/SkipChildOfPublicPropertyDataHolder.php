<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\SomeDataHolder;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\SomeService;

final class SkipChildOfPublicPropertyDataHolder extends SomeDataHolder
{
    public function __construct(?SomeService $minPrice)
    {
        parent::__construct($minPrice);
    }
}
