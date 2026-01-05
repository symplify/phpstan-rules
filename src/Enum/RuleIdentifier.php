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
    public const REQUIRE_ATTRIBUTE_NAME = 'symplify.requireAttributeName';

    /**
     * @var string
     */
    public const PHP_PARSER_NO_LEADING_BACKSLASH_IN_NAME = 'phpParser.noLeadingBackslashInName';

    /**
     * @var string
     */
    public const PARENT_METHOD_VISIBILITY_OVERRIDE = 'symplify.parentMethodVisibilityOverride';

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
    public const NO_VALUE_OBJECT_IN_SERVICE_CONSTRUCTOR = 'symplify.noValueObjectInServiceConstructor';

    /**
     * @var string
     */
    public const NO_DYNAMIC_NAME = 'symplify.noDynamicName';

    /**
     * @var string
     */
    public const NO_REFERENCE = 'symplify.noReference';

    /**
     * @var string
     */
    public const NO_CONSTRUCTOR_OVERRIDE = 'symplify.noConstructorOverride';

    /**
     * @var string
     */
    public const FORBIDDEN_NEW_INSTANCE = 'symplify.forbiddenNewInstance';

    /**
     * @var string
     */
    public const MAXIMUM_IGNORED_ERROR_COUNT = 'symplify.maximumIgnoredErrorCount';

    /**
     * @var string
     */
    public const STRING_FILE_ABSOLUTE_PATH_EXISTS = 'symplify.stringFileAbsolutePathExists';

    /**
     * @var string
     */
    public const NO_JUST_PROPERTY_ASSIGN = 'symplify.noJustPropertyAssign';

    /**
     * @var string
     */
    public const NO_PROTECTED_CLASS_STMT = 'symplify.noProtectedClassStmt';

    /**
     * @var string
     */
    public const CONVENTION_PARAM_NAME_TO_TYPE = 'symplify.conventionParamNameToType';

    /**
     * @var string
     */
    public const NO_ARRAY_MAP_WITH_ARRAY_CALLABLE = 'symplify.noArrayMapWithArrayCallable';

    /**
     * @var string
     */
    public const RULE_IDENTIFIER = 'symplify.foreachCeption';

    /**
     * @var string
     */
    public const NO_MISSING_VARIABLE_DIM_FETCH = 'symplify.noMissingVariableDimFetch';

    /**
     * @var string
     */
    public const NO_MISSNAMED_DOC_TAG = 'symplify.noMissnamedDocTag';
}
