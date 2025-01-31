<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequiredOnlyInAbstractRule\Fixture;

final class SkipCircularNote
{
    /**
     * Avoid circular dependency
     * @required
     */
    public function someMethod()
    {
    }
}
