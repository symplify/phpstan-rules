<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoClassReflectionStaticReflectionRule\Fixture;

use Exception;
use PHPStan\ShouldNotHappenException;

final class SkipNonReflectionNew
{
    public function check(): Exception
    {
        return new ShouldNotHappenException('Something');
    }
}
