<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoListenerWithoutContractRule\Fixture;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(event: 'postGenerateSchema')]
final class SkipDoctrineListenerAttribute
{
    public function postGenerateSchema(): void
    {
    }
}
