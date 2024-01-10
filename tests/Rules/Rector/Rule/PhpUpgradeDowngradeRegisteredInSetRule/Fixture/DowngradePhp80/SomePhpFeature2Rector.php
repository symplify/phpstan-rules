<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\PhpUpgradeDowngradeRegisteredInSetRule\Fixture\DowngradePhp80;

use Rector\Contract\Rector\RectorInterface;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class SomePhpFeature2Rector implements RectorInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
    }
}
