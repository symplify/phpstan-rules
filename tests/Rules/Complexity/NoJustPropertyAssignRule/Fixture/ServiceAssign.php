<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoJustPropertyAssignRule\Fixture;

use Doctrine\Persistence\ObjectManager;

final class ServiceAssign
{
    private ObjectManager $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function someMethod()
    {
        $manager = $this->manager;
    }
}
