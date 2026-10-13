<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\MockedMethodReturnMustMatchNativeTypeRule\Fixture;

use PHPUnit\Framework\MockObject\MockObject;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\MockedMethodReturnMustMatchNativeTypeRule\Source\SomeObject;

final class SkipMockedReturn
{
    public function run(SomeObject&MockObject $mock): void
    {
        $mock->method('getName')->willReturn('ok');
    }
}
