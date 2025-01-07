<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequiredOnlyInAbstractRule\Fixture;

abstract class SkipAbstractClass
{
    /**
     * @required
     */
    public function someMethod()
    {
    }
}
