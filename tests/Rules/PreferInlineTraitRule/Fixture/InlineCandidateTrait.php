<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferInlineTraitRule\Fixture;

trait InlineCandidateTrait
{
    public function someMethod(): void
    {
    }
}
