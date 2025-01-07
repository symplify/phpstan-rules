<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequiredOnlyInAbstractRule\Fixture;

final class NonAbstractControllerWithRequired
{
    /**
     * @required
     */
    public function someMethod()
    {
    }
}
