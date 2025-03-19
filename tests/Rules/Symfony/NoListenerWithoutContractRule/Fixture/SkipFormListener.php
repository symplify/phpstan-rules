<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoListenerWithoutContractRule\Fixture;

final class SkipFormListener
{
    public function process(\Symfony\Component\Form\Event\PostSubmitEvent $postSubmitEvent)
    {
    }
}
