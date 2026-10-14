<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDocumentMockingRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDocumentMockingRule\Source\Entity\SomeEntityRepository;

final class SkipRepositoryMocking extends TestCase
{
    public function test()
    {
        $repositoryMock = $this->createMock(SomeEntityRepository::class);
    }
}
