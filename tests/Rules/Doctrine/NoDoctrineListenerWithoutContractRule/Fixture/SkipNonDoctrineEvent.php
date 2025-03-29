<?php

namespace Symplify\PHPStanRules\Tests\Rules\Doctrine\NoDoctrineListenerWithoutContractRule\Fixture;

final class SkipNonDoctrineEvent
{
    public function onRequest()
    {
    }
}
