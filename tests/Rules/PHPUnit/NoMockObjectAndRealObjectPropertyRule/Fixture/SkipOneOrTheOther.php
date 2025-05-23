<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockObjectAndRealObjectPropertyRule\Fixture;

use PHPUnit\Framework\MockObject\MockObject;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockObjectAndRealObjectPropertyRule\Source\SomeObject;

final class SkipOneOrTheOther
{
    private SomeObject $someObject;

    private MockObject $anotherMock;
}
