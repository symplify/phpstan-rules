<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

use DateTimeInterface;

final class SkipNullableDateTime
{
    public function __construct(
        private readonly ?DateTimeInterface $createdAt,
    ) {
    }
}
