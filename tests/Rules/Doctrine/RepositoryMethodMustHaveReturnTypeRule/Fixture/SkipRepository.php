<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RepositoryMethodMustHaveReturnTypeRule\Fixture;

final class SkipRepository
{
    public function __construct()
    {
    }

    public function findActive(): array
    {
        return [];
    }

    private function helper()
    {
    }
}
