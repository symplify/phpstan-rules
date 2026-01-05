<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum\RuleIdentifier;

final class RectorRuleIdentifier
{
    public const string NO_INSTANCE_OF_STATIC_REFLECTION = 'rector.noInstanceOfStaticReflection';

    public const string UPGRADE_DOWNGRADE_REGISTERED_IN_SET = 'rector.upgradeDowngradeRegisteredInSet';

    public const string PHP_RULE_IMPLEMENTS_MIN_VERSION = 'rector.phpRuleImplementsMinVersion';

    public const string NO_CLASS_REFLECTION_STATIC_REFLECTION = 'rector.noClassReflectionStaticReflection';

    public const string NO_PROPERTY_NODE_ASSIGN = 'rector.noPropertyNodeAssign';

    public const string PREFER_DIRECT_IS_NAME = 'rector.preferDirectIsName';

    public const string NO_ONLY_NULL_RETURN_IN_REFACTOR = 'rector.noOnlyNullReturnInRefactor';

    public const string NO_INTEGER_REFACTOR_RETURN = 'rector.noIntegerRefactorReturn';

    public const string AVOID_FEATURE_SET_ATTRIBUTE_IN_RECTOR = 'rector.avoidFeatureSetAttributeInRector';
}
