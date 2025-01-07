<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class DoctrineRuleIdentifier
{
    public const DOCTRINE_NO_GET_REPOSITORY_OUTSIDE_SERVICE = 'doctrine.noGetRepositoryOutsideService';

    public const DOCTRINE_NO_REPOSITORY_CALL_IN_DATA_FIXTURES = 'doctrine.noRepositoryCallInDataFixtures';

    public const DOCTRINE_NO_PARENT_REPOSITORY = 'doctrine.noParentRepository';

    public const NO_ENTITY_MOCKING = 'doctrine.noEntityMocking';

    public const REQUIRE_QUERY_BUILDER_ON_REPOSITORY = 'doctrine.requireQueryBuilderOnRepository';
}
