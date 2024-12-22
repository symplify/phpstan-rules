<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class RuleIdentifier
{
    public const UPPERCASE_CONSTANT = 'symplify.uppercaseConstant';

    public const SEE_ANNOTATION_TO_TEST = 'symplify.seeAnnotationToTest';

    public const REQUIRE_ATTRIBUTE_NAME = 'symplify.requireAttributeName';

    public const RECTOR_PHP_RULE_IMPLEMENTS_MIN_VERSION = 'rector.phpRuleImplementsMinVersion';

    public const RECTOR_UPGRADE_DOWNGRADE_REGISTERED_IN_SET = 'rector.upgradeDowngradeRegisteredInSet';

    public const PHP_PARSER_NO_LEADING_BACKSLASH_IN_NAME = 'phpParser.noLeadingBackslashInName';

    public const RECTOR_NO_INSTANCE_OF_STATIC_REFLECTION = 'rector.noInstanceOfStaticReflection';

    public const RECTOR_NO_CLASS_REFLECTION_STATIC_REFLECTION = 'rector.noClassReflectionStaticReflection';

    public const PARENT_METHOD_VISIBILITY_OVERRIDE = 'symplify.parentMethodVisibilityOverride';

    public const NO_RETURN_SETTER_METHOD = 'symplify.noReturnSetterMethod';

    public const FORBIDDEN_STATIC_CLASS_CONST_FETCH = 'symplify.forbiddenStaticClassConstFetch';

    public const PREFERRED_CLASS = 'symplify.preferredClass';

    public const NO_TEST_MOCKS = 'symplify.noTestMocks';

    public const NO_GLOBAL_CONST = 'symplify.noGlobalConst';

    public const NO_ENTITY_OUTSIDE_ENTITY_NAMESPACE = 'symplify.noEntityOutsideEntityNamespace';

    public const FORBIDDEN_NODE = 'symplify.forbiddenNode';

    public const MULTIPLE_CLASS_LIKE_IN_FILE = 'symplify.multipleClassLikeInFile';

    public const FORBIDDEN_FUNC_CALL = 'symplify.forbiddenFuncCall';

    public const REQUIRE_ATTRIBUTE_NAMESPACE = 'symplify.requireAttributeNamespace';

    public const FORBIDDEN_ARRAY_METHOD_CALL = 'symplify.forbiddenArrayMethodCall';

    public const FORBIDDEN_EXTEND_OF_NON_ABSTRACT_CLASS = 'symplify.forbiddenExtendOfNonAbstractClass';

    public const EXPLICIT_ABSTRACT_PREFIX_NAME = 'symplify.explicitAbstractPrefixName';

    public const EXPLICIT_INTERFACE_SUFFIX_NAME = 'symplify.explicitInterfaceSuffixName';

    public const EXPLICIT_TRAIT_SUFFIX_NAME = 'symplify.explicitTraitSuffixName';

    public const REQUIRE_UNIQUE_ENUM_CONSTANT = 'symplify.requireUniqueEnumConstant';

    public const REQUIRE_EXCEPTION_NAMESPACE = 'symplify.requireExceptionNamespace';

    public const CLASS_NAME_RESPECTS_PARENT_SUFFIX = 'symplify.classNameRespectsParentSuffix';

    public const REQUIRED_INTERFACE_CONTRACT_NAMESPACE = 'symplify.requiredInterfaceContractNamespace';

    public const SYMFONY_REQUIRE_INVOKABLE_CONTROLLER = 'symfony.requireInvokableController';

    public const NO_VALUE_OBJECT_IN_SERVICE_CONSTRUCTOR = 'symplify.noValueObjectInServiceConstructor';

    public const DOCTRINE_NO_REPOSITORY_CALL_IN_DATA_FIXTURES = 'doctrine.noRepositoryCallInDataFixtures';

    public const PHPUNIT_NO_DOCUMENT_MOCKING = 'phpunit.noDocumentMocking';

    public const NO_DYNAMIC_NAME = 'symplify.noDynamicName';

    public const NO_REFERENCE = 'symplify.noReference';

    public const PHPUNIT_NO_MOCK_ONLY = 'phpunit.noMockOnly';

    public const SINGLE_ARG_EVENT_DISPATCH = 'symfony.singleArgEventDispatch';

    public const NO_ENTITY_MOCKING = 'doctrine.noEntityMocking';

    public const NO_STRING_IN_GET_SUBSCRIBED_EVENTS = 'symfony.noStringInGetSubscribedEvents';

    public const NO_LISTENER_WITHOUT_CONTRACT = 'symfony.noListenerWithoutContract';

    public const DOCTRINE_NO_PARENT_REPOSITORY = 'doctrine.noParentRepository';

    public const DOCTRINE_NO_GET_REPOSITORY_OUTSIDE_SERVICE = 'doctrine.noGetRepositoryOutsideService';

    public const SYMFONY_NO_REQUIRED_OUTSIDE_CLASS = 'symfony.noRequiredOutsideClass';

    public const NO_CONSTRUCTOR_OVERRIDE = 'symplify.noConstructorOverride';

    public const SYMFONY_NO_ABSTRACT_CONTROLLER_CONSTRUCTOR = 'symfony.noAbstractControllerConstructor';

    public const PHPUNIT_PUBLIC_STATIC_DATA_PROVIDER = 'phpunit.publicStaticDataProvider';

    public const FORBIDDEN_NEW_INSTANCE = 'symplify.forbiddenNewInstance';

    public const REQUIRE_QUERY_BUILDER_ON_REPOSITORY = 'doctrine.requireQueryBuilderOnRepository';

    public const NO_GET_IN_CONTROLLER = 'symfony.noGetInController';

    public const NO_GET_DOCTRINE_IN_CONTROLLER = 'symfony.noGetDoctrineInController';
}
