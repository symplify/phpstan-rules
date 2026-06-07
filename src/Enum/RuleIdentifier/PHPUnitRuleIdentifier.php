<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum\RuleIdentifier;

final class PHPUnitRuleIdentifier
{
    public const string NO_DOCUMENT_MOCKING = 'phpunit.noDocumentMocking';

    public const string NO_MOCK_ONLY = 'phpunit.noMockOnly';

    public const string PUBLIC_STATIC_DATA_PROVIDER = 'phpunit.publicStaticDataProvider';

    public const string NO_MOCK_OBJECT_AND_REAL_OBJECT_PROPERTY = 'phpunit.noMockObjectAndRealObjectProperty';

    public const string NO_ASSERT_FUNC_CALL_IN_TESTS = 'phpunit.noAssertFuncCallInTests';

    public const string NO_DOUBLE_CONSECUTIVE_TEST_MOCK = 'phpunit.noDoubleConsecutiveTestMock';

    public const string EXPLICIT_EXPECTS_MOCK_METHOD = 'phpunit.explicitExpectsMockMethod';

    public const string AVOID_ANY_EXPECTS = 'phpunit.avoidAnyExpects';

    public const string NO_WITH_ON_STUB = 'phpunit.noWithOnStub';

    public const string REQUIRE_AT_LEAST_ONE = 'phpunit.requireAtLeastOne';
}
