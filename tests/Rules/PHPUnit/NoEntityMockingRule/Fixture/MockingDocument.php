<?php

namespace Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoEntityMockingRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\PHPUnit\NoEntityMockingRule\Source\SomeDocument;

final class MockingDocument extends TestCase
{
    public function test(): void
    {
        $someDocumentMock = $this->createMock(SomeDocument::class);
    }
}
