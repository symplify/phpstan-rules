<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule\Fixture;

use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule\Source\FirstClass;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoMockOnlyTestRule\Source\SecondClass;

final class SkipConstraintValidatorTest extends ConstraintValidatorTestCase
{
    private \PHPUnit\Framework\MockObject\MockObject $firstMock;

    private \PHPUnit\Framework\MockObject\MockObject $secondMock;

    public function test()
    {
        $this->firstMock = $this->createMock(FirstClass::class);
        $this->secondMock = $this->createMock(SecondClass::class);
    }
}
