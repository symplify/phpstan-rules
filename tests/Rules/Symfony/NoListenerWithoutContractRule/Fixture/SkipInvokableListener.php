<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoListenerWithoutContractRule\Fixture;

use Symfony\Component\HttpKernel\Event\RequestEvent;

final class SkipInvokableListener
{
    public function __invoke(RequestEvent $event)
    {
    }
}
