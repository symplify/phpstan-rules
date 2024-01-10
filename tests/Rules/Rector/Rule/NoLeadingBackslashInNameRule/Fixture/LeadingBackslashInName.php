<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\NoLeadingBackslashInNameRule\Fixture;

use PhpParser\Node\Name;

final class LeadingBackslashInName
{
    public function run(): object
    {
        return new Name('\\Closure');
    }
}
