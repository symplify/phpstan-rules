<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoServiceSetterCallRule\Source;

final class SomeConsumer
{
    public function setSomeService(SomeService $someService): void
    {
    }

    public function configure(SomeService $someService): void
    {
    }
}
