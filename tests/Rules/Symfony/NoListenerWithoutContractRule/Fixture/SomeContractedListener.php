<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoListenerWithoutContractRule\Fixture;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SomeContractedListener implements EventSubscriberInterface
{
}
