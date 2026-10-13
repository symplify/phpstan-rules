<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReadonlyApiParamRule\Fixture;

final class ReportReadonlyApiParam
{
    /**
     * @param string $service @api
     */
    public function __construct(
        private readonly string $service,
    ) {
    }
}
