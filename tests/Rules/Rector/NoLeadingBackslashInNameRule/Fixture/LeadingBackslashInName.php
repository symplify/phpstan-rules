<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoLeadingBackslashInNameRule\Fixture;

use PhpParser\Node\Name;

final class LeadingBackslashInName
{
    public function run(): object
    {
        return new Name('\\Closure');
    }
}
