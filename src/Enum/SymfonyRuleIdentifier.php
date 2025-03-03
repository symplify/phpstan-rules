<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class SymfonyRuleIdentifier
{
    public const NO_GET_IN_CONTROLLER = 'symfony.noGetInController';

    public const NO_GET_IN_COMMAND = 'symfony.noGetInCommand';

    public const NO_GET_DOCTRINE_IN_CONTROLLER = 'symfony.noGetDoctrineInController';

    public const SINGLE_ARG_EVENT_DISPATCH = 'symfony.singleArgEventDispatch';

    public const NO_LISTENER_WITHOUT_CONTRACT = 'symfony.noListenerWithoutContract';

    public const SYMFONY_REQUIRE_INVOKABLE_CONTROLLER = 'symfony.requireInvokableController';

    public const SYMFONY_NO_REQUIRED_OUTSIDE_CLASS = 'symfony.noRequiredOutsideClass';

    public const NO_STRING_IN_GET_SUBSCRIBED_EVENTS = 'symfony.noStringInGetSubscribedEvents';

    public const SYMFONY_NO_ABSTRACT_CONTROLLER_CONSTRUCTOR = 'symfony.noAbstractControllerConstructor';

    public const SINGLE_REQUIRED_METHOD = 'symfony.singleRequiredMethod';

    public const SYMFONY_REQUIRED_ONLY_IN_ABSTRACT = 'symfony.requiredOnlyInAbstract';

    public const NO_CONSTRUCT_AND_REQUIRED = 'symfony.noConstructAndRequired';

    public const FORM_TYPE_CLASS_NAME = 'symfony.formTypeClassName';

    public const NO_ROUTING_PREFIX = 'symfony.noRoutingPrefix';

    public const NO_CLASS_LEVEL_ROUTE = 'symfony.noClassLevelRoute';

    public const NO_ROUTE_TRAILING_SLASH_PATH = 'symfony.noRouteTrailingSlashPath';

    public const NO_FIND_TAGGED_SERVICE_IDS_CALL = 'symfony.noFindTaggedServiceIdsCall';

    public const REQUIRE_ROUTE_NAME_TO_GENERATE_CONTROLLER_ROUTE = 'symfony.requireRouteNameToGenerateControllerRoute';
}
