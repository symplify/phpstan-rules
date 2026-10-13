<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\RepositoryMethodMustHaveReturnTypeRule\Fixture;

final class SkipNonRepositoryClass
{
    public function findActive()
    {
    }
}
