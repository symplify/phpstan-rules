<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\PHPUnit\Rules\NoTestMocksRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SomeMocking extends TestCase
{
    public function test()
    {
        $someClassMock = $this->createMock('SomeClass');
    }
}
