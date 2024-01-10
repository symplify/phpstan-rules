<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\PhpUpgradeDowngradeRegisteredInSetRule\Fixture\Php80;

use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Contract\Rector\RectorInterface;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class SkipConfigurableRector implements RectorInterface, ConfigurableRectorInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
    }

    public function configure(array $configuration): void
    {
    }
}
