<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum\RuleIdentifier;

final class SymfonyRuleIdentifier
{
    /**
     * @var string
     */
    public const NO_GET_IN_CONTROLLER = 'symfony.noGetInController';

    /**
     * @var string
     */
    public const NO_GET_IN_COMMAND = 'symfony.noGetInCommand';

    /**
     * @var string
     */
    public const NO_GET_DOCTRINE_IN_CONTROLLER = 'symfony.noGetDoctrineInController';

    /**
     * @var string
     */
    public const SINGLE_ARG_EVENT_DISPATCH = 'symfony.singleArgEventDispatch';

    /**
     * @var string
     */
    public const NO_LISTENER_WITHOUT_CONTRACT = 'symfony.noListenerWithoutContract';

    /**
     * @var string
     */
    public const SYMFONY_REQUIRE_INVOKABLE_CONTROLLER = 'symfony.requireInvokableController';

    /**
     * @var string
     */
    public const SYMFONY_NO_REQUIRED_OUTSIDE_CLASS = 'symfony.noRequiredOutsideClass';

    /**
     * @var string
     */
    public const NO_STRING_IN_GET_SUBSCRIBED_EVENTS = 'symfony.noStringInGetSubscribedEvents';

    /**
     * @var string
     */
    public const SYMFONY_NO_ABSTRACT_CONTROLLER_CONSTRUCTOR = 'symfony.noAbstractControllerConstructor';

    /**
     * @var string
     */
    public const SINGLE_REQUIRED_METHOD = 'symfony.singleRequiredMethod';

    /**
     * @var string
     */
    public const SYMFONY_REQUIRED_ONLY_IN_ABSTRACT = 'symfony.requiredOnlyInAbstract';

    /**
     * @var string
     */
    public const NO_CONSTRUCT_AND_REQUIRED = 'symfony.noConstructAndRequired';

    /**
     * @var string
     */
    public const FORM_TYPE_CLASS_NAME = 'symfony.formTypeClassName';

    /**
     * @var string
     */
    public const NO_ROUTING_PREFIX = 'symfony.noRoutingPrefix';

    /**
     * @var string
     */
    public const NO_CLASS_LEVEL_ROUTE = 'symfony.noClassLevelRoute';

    /**
     * @var string
     */
    public const NO_ROUTE_TRAILING_SLASH_PATH = 'symfony.noRouteTrailingSlashPath';

    /**
     * @var string
     */
    public const NO_FIND_TAGGED_SERVICE_IDS_CALL = 'symfony.noFindTaggedServiceIdsCall';

    /**
     * @var string
     */
    public const REQUIRE_ROUTE_NAME_TO_GENERATE_CONTROLLER_ROUTE = 'symfony.requireRouteNameToGenerateControllerRoute';

    /**
     * @var string
     */
    public const SERVICES_EXCLUDED_DIRECTORY_MUST_EXIST = 'symfony.servicesExcludedDirectoryMustExist';

    /**
     * @var string
     */
    public const NO_BUNDLE_RESOURCE_CONFIG = 'symfony.noBundleResourceConfig';

    /**
     * @var string
     */
    public const ALREADY_REGISTERED_AUTODISCOVERY_SERVICE = 'symfony.alreadyRegisteredAutodiscoveryService';

    /**
     * @var string
     */
    public const NO_DUPLICATE_ARGS_AUTOWIRE_BY_TYPE = 'symfony.noDuplicateArgsAutowireByType';

    /**
     * @var string
     */
    public const NO_DUPLICATE_ARG_AUTOWIRE_BY_TYPE = 'symfony.noDuplicateArgAutowireByType';

    /**
     * @var string
     */
    public const NO_SERVICE_SAME_NAME_SET_CLASS = 'symfony.noServiceSameNameSetClass';

    /**
     * @var string
     */
    public const REQUIRED_IS_GRANTED_ENUM = 'symfony.requiredIsGrantedEnum';

    /**
     * @var string
     */
    public const PREFER_AUTOWIRE_ATTRIBUTE_OVER_CONFIG_PARAM = 'symfony.preferAutowireAttributeOverConfigParam';

    /**
     * @var string
     */
    public const RULE_IDENTIFIER = 'symfony.noServiceAutowireDuplicate';

    /**
     * @var string
     */
    public const NO_SET_CLASS_SERVICE_DUPLICATE = 'symfony.noSetClassServiceDuplicate';

    /**
     * @var string
     */
    public const NO_CONTROLLER_METHOD_INJECTION = 'symfony.noControllerMethodInjection';

    /**
     * @var string
     */
    public const FILE_NAME_MATCHES_EXTENSION = 'symfony.fileNameMatchesExtension';
}
