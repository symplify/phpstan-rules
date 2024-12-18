<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoEntityMockingRule\Fixture;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Handyman\Tests\PHPStan\Rule\NoEntityMockingRule\Source\SomeDocument;

final class MockingDocument extends TestCase
{
    public function test(): void
    {
        $someDocumentMock = $this->createMock(SomeDocument::class);
    }
}
