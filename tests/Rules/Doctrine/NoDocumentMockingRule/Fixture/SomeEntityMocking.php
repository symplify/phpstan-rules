<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDocumentMockingRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDocumentMockingRule\Source\Entity\SomeEntity;

final class SomeEntityMocking extends TestCase
{
    public function test()
    {
        $someMock = $this->createMock(SomeEntity::class);
    }
}
