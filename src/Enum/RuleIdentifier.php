<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class RuleIdentifier
{
    public const string UPPERCASE_CONSTANT = 'symplify.uppercaseConstant';

    public const string SEE_ANNOTATION_TO_TEST = 'symplify.seeAnnotationToTest';

    public const string REQUIRE_ATTRIBUTE_NAME = 'symplify.requireAttributeName';

    public const string PHP_PARSER_NO_LEADING_BACKSLASH_IN_NAME = 'phpParser.noLeadingBackslashInName';

    public const string PARENT_METHOD_VISIBILITY_OVERRIDE = 'symplify.parentMethodVisibilityOverride';

    public const string NO_RETURN_SETTER_METHOD = 'symplify.noReturnSetterMethod';

    public const string FORBIDDEN_STATIC_CLASS_CONST_FETCH = 'symplify.forbiddenStaticClassConstFetch';

    public const string PREFERRED_CLASS = 'symplify.preferredClass';

    public const string NO_TEST_MOCKS = 'symplify.noTestMocks';

    public const string NO_GLOBAL_CONST = 'symplify.noGlobalConst';

    public const string NO_ENTITY_OUTSIDE_ENTITY_NAMESPACE = 'symplify.noEntityOutsideEntityNamespace';

    public const string FORBIDDEN_NODE = 'symplify.forbiddenNode';

    public const string MULTIPLE_CLASS_LIKE_IN_FILE = 'symplify.multipleClassLikeInFile';

    public const string FORBIDDEN_FUNC_CALL = 'symplify.forbiddenFuncCall';

    public const string REQUIRE_ATTRIBUTE_NAMESPACE = 'symplify.requireAttributeNamespace';

    public const string FORBIDDEN_ARRAY_METHOD_CALL = 'symplify.forbiddenArrayMethodCall';

    public const string FORBIDDEN_EXTEND_OF_NON_ABSTRACT_CLASS = 'symplify.forbiddenExtendOfNonAbstractClass';

    public const string EXPLICIT_ABSTRACT_PREFIX_NAME = 'symplify.explicitAbstractPrefixName';

    public const string EXPLICIT_INTERFACE_SUFFIX_NAME = 'symplify.explicitInterfaceSuffixName';

    public const string EXPLICIT_TRAIT_SUFFIX_NAME = 'symplify.explicitTraitSuffixName';

    public const string REQUIRE_UNIQUE_ENUM_CONSTANT = 'symplify.requireUniqueEnumConstant';

    public const string REQUIRE_EXCEPTION_NAMESPACE = 'symplify.requireExceptionNamespace';

    public const string CLASS_NAME_RESPECTS_PARENT_SUFFIX = 'symplify.classNameRespectsParentSuffix';

    public const string REQUIRED_INTERFACE_CONTRACT_NAMESPACE = 'symplify.requiredInterfaceContractNamespace';

    public const string NO_VALUE_OBJECT_IN_SERVICE_CONSTRUCTOR = 'symplify.noValueObjectInServiceConstructor';

    public const string NO_DYNAMIC_NAME = 'symplify.noDynamicName';

    public const string NO_REFERENCE = 'symplify.noReference';

    public const string NO_CONSTRUCTOR_OVERRIDE = 'symplify.noConstructorOverride';

    public const string FORBIDDEN_NEW_INSTANCE = 'symplify.forbiddenNewInstance';

    public const string MAXIMUM_IGNORED_ERROR_COUNT = 'symplify.maximumIgnoredErrorCount';

    public const string STRING_FILE_ABSOLUTE_PATH_EXISTS = 'symplify.stringFileAbsolutePathExists';

    public const string NO_JUST_PROPERTY_ASSIGN = 'symplify.noJustPropertyAssign';

    public const string NO_PROTECTED_CLASS_STMT = 'symplify.noProtectedClassStmt';

    public const string CONVENTION_PARAM_NAME_TO_TYPE = 'symplify.conventionParamNameToType';

    public const string NO_ARRAY_MAP_WITH_ARRAY_CALLABLE = 'symplify.noArrayMapWithArrayCallable';

    public const string RULE_IDENTIFIER = 'symplify.foreachCeption';

    public const string NO_MISSING_VARIABLE_DIM_FETCH = 'symplify.noMissingVariableDimFetch';

    public const string NO_MISSNAMED_DOC_TAG = 'symplify.noMissnamedDocTag';
}
