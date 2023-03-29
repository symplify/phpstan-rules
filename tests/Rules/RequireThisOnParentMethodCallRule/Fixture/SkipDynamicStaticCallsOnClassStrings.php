<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisOnParentMethodCallRule\Fixture;

use PHPUnit\Framework\TestCase;

class SkipDynamicStaticCallsOnClassStrings extends ParentClass
{
    public function foo()
    {
        $testClass = TestCase::class;
        $testClass::isEmpty();
    }
}
