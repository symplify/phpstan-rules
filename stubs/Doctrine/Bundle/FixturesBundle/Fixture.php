<?php

namespace Doctrine\Bundle\FixturesBundle;

use Doctrine\Common\DataFixtures\FixtureInterface;

if (class_exists('Doctrine\Bundle\FixturesBundle\Fixture')) {
    return;
}


class Fixture implements FixtureInterface
{

}
