<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class RuleIdentifier
{
    /**
     * @var string
     */
    public const UPPERCASE_CONSTANT = 'symplify.uppercaseConstant';

    /**
     * @var string
     */
    public const SEE_ANNOTATION_TO_TEST = 'symplify.seeAnnotationToTest';

    /**
     * @var string
     */
    public const REGEX_SUFFIX_IN_REGEX_CONSTANT = 'symplify.regexSuffixInRegexConstant';

    /**
     * @var string
     */
    public const REQUIRE_ATTRIBUTE_NAME = 'symplify.requireAttributeName';

    /**
     * @var string
     */
    public const RECTOR_PHP_RULE_IMPLEMENTS_MIN_VERSION = 'rector.phpRuleImplementsMinVersion';

    /**
     * @var string
     */
    public const RECTOR_UPGRADE_DOWNGRADE_REGISTERED_IN_SET = 'rector.upgradeDowngradeRegisteredInSet';

    /**
     * @var string
     */
    public const PHP_PARSER_NO_LEADING_BACKSLASH_IN_NAME = 'phpParser.noLeadingBackslashInName';

    /**
     * @var string
     */
    public const NO_SINGLE_INTERFACE_IMPLEMENTER = 'symplify.noSingleInterfaceImplementer';

    /**
     * @var string
     */
    public const NO_RETURN_ARRAY_VARIABLE_LIST = 'symplify.noReturnArrayVariableList';

    /**
     * @var string
     */
    public const RECTOR_NO_INSTANCE_OF_STATIC_REFLECTION = 'rector.noInstanceOfStaticReflection';

    /**
     * @var string
     */
    public const RECTOR_NO_CLASS_REFLECTION_STATIC_REFLECTION = 'rector.noClassReflectionStaticReflection';

    /**
     * @var string
     */
    public const PARENT_METHOD_VISIBILITY_OVERRIDE = 'symplify.parentMethodVisibilityOverride';

    /**
     * @var string
     */
    public const CLASS_CONSTANT_REGEX = 'symplify.classConstantRegex';

    /**
     * @var string
     */
    public const NO_RETURN_SETTER_METHOD = 'symplify.noReturnSetterMethod';

    /**
     * @var string
     */
    public const FORBIDDEN_STATIC_CLASS_CONST_FETCH = 'symplify.forbiddenStaticClassConstFetch';

    /**
     * @var string
     */
    public const PREFERRED_CLASS = 'symplify.preferredClass';

    /**
     * @var string
     */
    public const NO_TEST_MOCKS = 'symplify.noTestMocks';

    /**
     * @var string
     */
    public const NO_GLOBAL_CONST = 'symplify.noGlobalConst';

    /**
     * @var string
     */
    public const NO_ENTITY_OUTSIDE_ENTITY_NAMESPACE = 'symplify.noEntityOutsideEntityNamespace';

    /**
     * @var string
     */
    public const FORBIDDEN_NODE = 'symplify.forbiddenNode';

    /**
     * @var string
     */
    public const MULTIPLE_CLASS_LIKE_IN_FILE = 'symplify.multipleClassLikeInFile';

    /**
     * @var string
     */
    public const FORBIDDEN_FUNC_CALL = 'symplify.forbiddenFuncCall';

    /**
     * @var string
     */
    public const REQUIRE_ATTRIBUTE_NAMESPACE = 'symplify.requireAttributeNamespace';

    /**
     * @var string
     */
    public const FORBIDDEN_ARRAY_METHOD_CALL = 'symplify.forbiddenArrayMethodCall';

    /**
     * @var string
     */
    public const REGEX_ANNOTATE_CLASS_CONST = 'symplify.regexAnnotateClassConst';

    /**
     * @var string
     */
    public const FORBIDDEN_EXTEND_OF_NON_ABSTRACT_CLASS = 'symplify.forbiddenExtendOfNonAbstractClass';

    /**
     * @var string
     */
    public const EXPLICIT_ABSTRACT_PREFIX_NAME = 'symplify.explicitAbstractPrefixName';

    /**
     * @var string
     */
    public const EXPLICIT_INTERFACE_SUFFIX_NAME = 'symplify.explicitInterfaceSuffixName';

    /**
     * @var string
     */
    public const EXPLICIT_TRAIT_SUFFIX_NAME = 'symplify.explicitTraitSuffixName';

    /**
     * @var string
     */
    public const REQUIRE_UNIQUE_ENUM_CONSTANT = 'symplify.requireUniqueEnumConstant';

    /**
     * @var string
     */
    public const REQUIRE_EXCEPTION_NAMESPACE = 'symplify.requireExceptionNamespace';

    /**
     * @var string
     */
    public const CLASS_NAME_RESPECTS_PARENT_SUFFIX = 'symplify.classNameRespectsParentSuffix';

    /**
     * @var string
     */
    public const REQUIRED_INTERFACE_CONTRACT_NAMESPACE = 'symplify.requiredInterfaceContractNamespace';

    /**
     * @var string
     */
    public const SYMFONY_REQUIRE_INVOKABLE_CONTROLLER = 'symfony.requireInvokableController';
}
