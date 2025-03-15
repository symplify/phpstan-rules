<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoListenerWithoutContractRule\Fixture;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class SomeContractedWithAttributeListener
{
}
