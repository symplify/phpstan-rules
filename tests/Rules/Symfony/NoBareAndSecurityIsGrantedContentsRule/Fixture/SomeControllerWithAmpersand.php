<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoBareAndSecurityIsGrantedContentsRule\Fixture;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('has_role("some_resource") && is_granted("another_resource")')]
final class SomeControllerWithAmpersand
{
    public function run()
    {
    }
}
