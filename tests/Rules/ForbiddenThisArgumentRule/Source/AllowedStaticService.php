<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Source;

final class AllowedStaticService
{
    public static function someMethod($value)
    {
    }
}
