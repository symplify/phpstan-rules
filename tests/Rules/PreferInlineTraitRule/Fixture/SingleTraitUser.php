<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferInlineTraitRule\Fixture;

final class SingleTraitUser
{
    use InlineCandidateTrait;
}
