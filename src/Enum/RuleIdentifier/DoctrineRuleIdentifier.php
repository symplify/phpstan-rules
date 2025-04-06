<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum\RuleIdentifier;

final class DoctrineRuleIdentifier
{
    public const NO_GET_REPOSITORY_OUTSIDE_SERVICE = 'doctrine.noGetRepositoryOutsideService';

    public const NO_REPOSITORY_CALL_IN_DATA_FIXTURES = 'doctrine.noRepositoryCallInDataFixtures';

    public const NO_PARENT_REPOSITORY = 'doctrine.noParentRepository';

    public const NO_ENTITY_MOCKING = 'doctrine.noEntityMocking';

    public const REQUIRE_QUERY_BUILDER_ON_REPOSITORY = 'doctrine.requireQueryBuilderOnRepository';

    public const INJECT_SERVICE_REPOSITORY = 'doctrine.injectServiceRepository';

    public const NO_LISTENER_WITHOUT_CONTRACT = 'doctrine.noListenerWithoutContract';

    public const REQUIRE_SERVICE_PARENT_REPOSITORY = 'doctrine.requireServiceParentRepository';
}
