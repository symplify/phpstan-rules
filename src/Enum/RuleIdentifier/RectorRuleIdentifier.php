<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum\RuleIdentifier;

final class RectorRuleIdentifier
{
    public const NO_INSTANCE_OF_STATIC_REFLECTION = 'rector.noInstanceOfStaticReflection';

    public const UPGRADE_DOWNGRADE_REGISTERED_IN_SET = 'rector.upgradeDowngradeRegisteredInSet';

    public const PHP_RULE_IMPLEMENTS_MIN_VERSION = 'rector.phpRuleImplementsMinVersion';

    public const NO_CLASS_REFLECTION_STATIC_REFLECTION = 'rector.noClassReflectionStaticReflection';
}
