<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDuplicateNonRepeatableAttributeRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoDuplicateNonRepeatableAttributeRule\Source\SingleAttribute;

final class ReportDuplicateAttribute
{
    #[SingleAttribute]
    #[SingleAttribute]
    private string $name = '';
}
