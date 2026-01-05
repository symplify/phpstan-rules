<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum\RuleIdentifier;

final class DoctrineRuleIdentifier
{
    /**
     * @var string
     */
    public const NO_GET_REPOSITORY_OUTSIDE_SERVICE = 'doctrine.noGetRepositoryOutsideService';

    /**
     * @var string
     */
    public const NO_REPOSITORY_CALL_IN_DATA_FIXTURES = 'doctrine.noRepositoryCallInDataFixtures';

    /**
     * @var string
     */
    public const NO_PARENT_REPOSITORY = 'doctrine.noParentRepository';

    /**
     * @var string
     */
    public const NO_ENTITY_MOCKING = 'doctrine.noEntityMocking';

    /**
     * @var string
     */
    public const REQUIRE_QUERY_BUILDER_ON_REPOSITORY = 'doctrine.requireQueryBuilderOnRepository';

    /**
     * @var string
     */
    public const INJECT_SERVICE_REPOSITORY = 'doctrine.injectServiceRepository';

    /**
     * @var string
     */
    public const NO_LISTENER_WITHOUT_CONTRACT = 'doctrine.noListenerWithoutContract';

    /**
     * @var string
     */
    public const REQUIRE_SERVICE_PARENT_REPOSITORY = 'doctrine.requireServiceParentRepository';
}
