<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoLeadingBackslashInNameRule\Fixture;

use PhpParser\Node\Name\FullyQualified;

final class SkipUseFullyQualified
{
    public function run(): object
    {
        return new FullyQualified('Closure');
    }
}
