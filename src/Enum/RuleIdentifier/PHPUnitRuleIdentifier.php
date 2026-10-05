<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum\RuleIdentifier;

final class PHPUnitRuleIdentifier
{
    /**
     * @var string
     */
    public const NO_DOCUMENT_MOCKING = 'phpunit.noDocumentMocking';

    /**
     * @var string
     */
    public const NO_MOCK_ONLY = 'phpunit.noMockOnly';

    /**
     * @var string
     */
    public const PUBLIC_STATIC_DATA_PROVIDER = 'phpunit.publicStaticDataProvider';

    /**
     * @var string
     */
    public const NO_MOCK_OBJECT_AND_REAL_OBJECT_PROPERTY = 'phpunit.noMockObjectAndRealObjectProperty';

    /**
     * @var string
     */
    public const NO_ASSERT_FUNC_CALL_IN_TESTS = 'phpunit.noAssertFuncCallInTests';

    /**
     * @var string
     */
    public const NO_DOUBLE_CONSECUTIVE_TEST_MOCK = 'phpunit.noDoubleConsecutiveTestMock';

    /**
     * @var string
     */
    public const EXPLICIT_EXPECTS_MOCK_METHOD = 'phpunit.explicitExpectsMockMethod';

    /**
     * @var string
     */
    public const AVOID_ANY_EXPECTS = 'phpunit.avoidAnyExpects';

    /**
     * @var string
     */
    public const NO_WITH_ON_STUB = 'phpunit.noWithOnStub';

    /**
     * @var string
     */
    public const REQUIRE_AT_LEAST_ONE = 'phpunit.requireAtLeastOne';
}
