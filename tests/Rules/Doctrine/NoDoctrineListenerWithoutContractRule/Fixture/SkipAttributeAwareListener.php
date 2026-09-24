<?php

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDoctrineListenerWithoutContractRule\Fixture;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(event: 'prePersist')]
final class SkipAttributeAwareListener
{
    public function prePersist()
    {
    }
}
