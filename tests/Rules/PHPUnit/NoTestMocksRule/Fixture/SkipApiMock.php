<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoTestMocksRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\PHPUnit\Rules\NoTestMocksRule\Source\SomeAllowedType;

final class SkipApiMock extends TestCase
{
    public function test()
    {
        $someAllowedTypeMock = $this->createMock(SomeAllowedType::class);
    }
}
