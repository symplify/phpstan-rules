<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoInstanceOfStaticReflectionRule\Fixture;

use Symplify\SmartFileSystem\SmartFileInfo;

final class SkipFileInfo
{
    public function check(object $object): bool
    {
        if ($object instanceof SmartFileInfo) {
            return true;
        }

        return false;
    }
}
