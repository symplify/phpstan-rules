<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum\RuleIdentifier;

final class RectorRuleIdentifier
{
    /**
     * @var string
     */
    public const NO_INSTANCE_OF_STATIC_REFLECTION = 'rector.noInstanceOfStaticReflection';

    /**
     * @var string
     */
    public const UPGRADE_DOWNGRADE_REGISTERED_IN_SET = 'rector.upgradeDowngradeRegisteredInSet';

    /**
     * @var string
     */
    public const PHP_RULE_IMPLEMENTS_MIN_VERSION = 'rector.phpRuleImplementsMinVersion';

    /**
     * @var string
     */
    public const NO_CLASS_REFLECTION_STATIC_REFLECTION = 'rector.noClassReflectionStaticReflection';

    /**
     * @var string
     */
    public const NO_PROPERTY_NODE_ASSIGN = 'rector.noPropertyNodeAssign';

    /**
     * @var string
     */
    public const PREFER_DIRECT_IS_NAME = 'rector.preferDirectIsName';

    /**
     * @var string
     */
    public const NO_ONLY_NULL_RETURN_IN_REFACTOR = 'rector.noOnlyNullReturnInRefactor';

    /**
     * @var string
     */
    public const NO_INTEGER_REFACTOR_RETURN = 'rector.noIntegerRefactorReturn';

    /**
     * @var string
     */
    public const AVOID_FEATURE_SET_ATTRIBUTE_IN_RECTOR = 'rector.avoidFeatureSetAttributeInRector';
}
