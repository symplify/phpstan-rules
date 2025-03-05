<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Enum;

final class MethodName
{
    /**
     * @var string
     */
    public const INVOKE = '__invoke';

    /**
     * @var string
     */
    public const CONSTRUCTOR = '__construct';

    /**
     * @var string
     */
    public const SET_UP = 'setUp';

    /**
     * @var string
     */
    public const TEAR_DOWN = 'tearDown';
}
