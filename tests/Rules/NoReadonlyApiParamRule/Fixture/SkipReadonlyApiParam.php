<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReadonlyApiParamRule\Fixture;

final class SkipReadonlyApiParam
{
    /**
     * @param string $plain
     * @param string $swappable @api
     */
    public function __construct(
        private readonly string $plain,
        private string $swappable,
    ) {
    }
}
