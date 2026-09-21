<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoReadonlyEntityClassRule\Fixture;

final class SkipNonReadonlyEntity
{
    public static function loadMetadata(object $metadata): void
    {
    }
}
