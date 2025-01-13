<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoListenerWithoutContractRule\Fixture;

final class SkipDoctrineListener
{
    public function onFlush(\Doctrine\ODM\MongoDB\Event\OnFlushEventArgs $onFlushEventArgs)
    {
    }
}
