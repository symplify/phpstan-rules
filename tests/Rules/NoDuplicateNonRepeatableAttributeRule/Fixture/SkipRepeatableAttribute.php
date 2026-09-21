<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDuplicateNonRepeatableAttributeRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoDuplicateNonRepeatableAttributeRule\Source\RepeatableAttribute;

final class SkipRepeatableAttribute
{
    #[RepeatableAttribute]
    #[RepeatableAttribute]
    private string $name = '';
}
