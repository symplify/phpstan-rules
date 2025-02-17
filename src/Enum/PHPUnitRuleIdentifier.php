<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class PHPUnitRuleIdentifier
{
    public const NO_DOCUMENT_MOCKING = 'phpunit.noDocumentMocking';

    public const NO_MOCK_ONLY = 'phpunit.noMockOnly';

    public const PUBLIC_STATIC_DATA_PROVIDER = 'phpunit.publicStaticDataProvider';

    public const NO_MOCK_OBJECT_AND_REAL_OBJECT_PROPERTY = 'phpunit.noMockObjectAndRealObjectProperty';

    public const NO_ASSERT_FUNC_CALL_IN_TESTS = 'phpunit.noAssertFuncCallInTests';
}
