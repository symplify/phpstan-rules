<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\PhpUpgradeDowngradeRegisteredInSetRule\Fixture\Php80;

use Rector\Configuration\Deprecation\Contract\DeprecatedInterface;
use Rector\Contract\Rector\RectorInterface;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class SkipDeprecatedRector implements RectorInterface, DeprecatedInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
    }
}
