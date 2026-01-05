<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class MethodName
{
    public const string INVOKE = '__invoke';

    public const string CONSTRUCTOR = '__construct';

    public const string SET_UP = 'setUp';

    public const string TEAR_DOWN = 'tearDown';
}
