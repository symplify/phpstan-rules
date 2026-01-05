<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum\RuleIdentifier;

final class SymfonyRuleIdentifier
{
    public const string NO_GET_IN_CONTROLLER = 'symfony.noGetInController';

    public const string NO_GET_IN_COMMAND = 'symfony.noGetInCommand';

    public const string NO_GET_DOCTRINE_IN_CONTROLLER = 'symfony.noGetDoctrineInController';

    public const string SINGLE_ARG_EVENT_DISPATCH = 'symfony.singleArgEventDispatch';

    public const string NO_LISTENER_WITHOUT_CONTRACT = 'symfony.noListenerWithoutContract';

    public const string SYMFONY_REQUIRE_INVOKABLE_CONTROLLER = 'symfony.requireInvokableController';

    public const string SYMFONY_NO_REQUIRED_OUTSIDE_CLASS = 'symfony.noRequiredOutsideClass';

    public const string NO_STRING_IN_GET_SUBSCRIBED_EVENTS = 'symfony.noStringInGetSubscribedEvents';

    public const string SYMFONY_NO_ABSTRACT_CONTROLLER_CONSTRUCTOR = 'symfony.noAbstractControllerConstructor';

    public const string SINGLE_REQUIRED_METHOD = 'symfony.singleRequiredMethod';

    public const string SYMFONY_REQUIRED_ONLY_IN_ABSTRACT = 'symfony.requiredOnlyInAbstract';

    public const string NO_CONSTRUCT_AND_REQUIRED = 'symfony.noConstructAndRequired';

    public const string FORM_TYPE_CLASS_NAME = 'symfony.formTypeClassName';

    public const string NO_ROUTING_PREFIX = 'symfony.noRoutingPrefix';

    public const string NO_CLASS_LEVEL_ROUTE = 'symfony.noClassLevelRoute';

    public const string NO_ROUTE_TRAILING_SLASH_PATH = 'symfony.noRouteTrailingSlashPath';

    public const string NO_FIND_TAGGED_SERVICE_IDS_CALL = 'symfony.noFindTaggedServiceIdsCall';

    public const string REQUIRE_ROUTE_NAME_TO_GENERATE_CONTROLLER_ROUTE = 'symfony.requireRouteNameToGenerateControllerRoute';

    public const string SERVICES_EXCLUDED_DIRECTORY_MUST_EXIST = 'symfony.servicesExcludedDirectoryMustExist';

    public const string NO_BUNDLE_RESOURCE_CONFIG = 'symfony.noBundleResourceConfig';

    public const string ALREADY_REGISTERED_AUTODISCOVERY_SERVICE = 'symfony.alreadyRegisteredAutodiscoveryService';

    public const string NO_DUPLICATE_ARGS_AUTOWIRE_BY_TYPE = 'symfony.noDuplicateArgsAutowireByType';

    public const string NO_DUPLICATE_ARG_AUTOWIRE_BY_TYPE = 'symfony.noDuplicateArgAutowireByType';

    public const string NO_SERVICE_SAME_NAME_SET_CLASS = 'symfony.noServiceSameNameSetClass';

    public const string REQUIRED_IS_GRANTED_ENUM = 'symfony.requiredIsGrantedEnum';

    public const string PREFER_AUTOWIRE_ATTRIBUTE_OVER_CONFIG_PARAM = 'symfony.preferAutowireAttributeOverConfigParam';

    public const string RULE_IDENTIFIER = 'symfony.noServiceAutowireDuplicate';

    public const string NO_SET_CLASS_SERVICE_DUPLICATE = 'symfony.noSetClassServiceDuplicate';

    public const string NO_CONTROLLER_METHOD_INJECTION = 'symfony.noControllerMethodInjection';

    public const string FILE_NAME_MATCHES_EXTENSION = 'symfony.fileNameMatchesExtension';
}
