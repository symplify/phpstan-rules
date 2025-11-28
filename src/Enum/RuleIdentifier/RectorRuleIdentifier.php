<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum\RuleIdentifier;

final class RectorRuleIdentifier
{
    public const NO_INSTANCE_OF_STATIC_REFLECTION = 'rector.noInstanceOfStaticReflection';

    public const UPGRADE_DOWNGRADE_REGISTERED_IN_SET = 'rector.upgradeDowngradeRegisteredInSet';

    public const PHP_RULE_IMPLEMENTS_MIN_VERSION = 'rector.phpRuleImplementsMinVersion';

    public const NO_CLASS_REFLECTION_STATIC_REFLECTION = 'rector.noClassReflectionStaticReflection';

    public const NO_PROPERTY_NODE_ASSIGN = 'rector.noPropertyNodeAssign';

    public const PREFER_DIRECT_IS_NAME = 'rector.preferDirectIsName';

    public const NO_ONLY_NULL_RETURN_IN_REFACTOR = 'rector.noOnlyNullReturnInRefactor';
}
