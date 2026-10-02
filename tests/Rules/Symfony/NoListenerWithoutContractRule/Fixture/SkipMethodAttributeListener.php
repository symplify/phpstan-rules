<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoListenerWithoutContractRule\Fixture;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class SkipMethodAttributeListener
{
    #[AsEventListener(event: 'kernel.terminate')]
    public function onKernelTerminate(): void
    {
    }
}
