<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Source\AllowedStaticService;

final class SkipAllowedStaticCall
{
    public function run()
    {
        AllowedStaticService::someMethod($this);
    }
}
