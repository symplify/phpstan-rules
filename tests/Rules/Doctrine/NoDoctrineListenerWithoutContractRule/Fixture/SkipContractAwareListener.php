<?php

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDoctrineListenerWithoutContractRule\Fixture;

use Doctrine\Common\EventSubscriber;

final class SkipContractAwareListener implements EventSubscriber
{
    public function preFlush()
    {
    }
}
