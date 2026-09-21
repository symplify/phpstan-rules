<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

final class SkipNullableScalar
{
    /**
     * @param mixed[]|null $options
     */
    public function __construct(
        private readonly ?string $name,
        private readonly ?array $options,
    ) {
    }
}
