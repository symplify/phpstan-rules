<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\MockedMethodReturnMustMatchNativeTypeRule\Source;

class SomeObject
{
    public function getName(): string
    {
        return '';
    }
}
