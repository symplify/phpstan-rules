<?php

declare(strict_types=1);

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoRepositoryCallInDataFixtureRule\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class SomeRepositoryCallInFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $notAllowed = $manager->getRepository('someEntity');

        $alsoNotAllowed = $notAllowed->find(5);
    }
}
