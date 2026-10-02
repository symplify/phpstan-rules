<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source;

abstract class SomeDataHolder
{
    public function __construct(
        public readonly ?SomeService $minPrice,
    ) {
    }
}
