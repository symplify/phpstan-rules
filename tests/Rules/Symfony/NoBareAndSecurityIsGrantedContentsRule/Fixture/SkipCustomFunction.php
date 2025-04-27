<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoBareAndSecurityIsGrantedContentsRule\Fixture;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('custom_check("some_resource") && custom_check("another_resource")')]
final class SkipCustomFunction
{
    public function run()
    {
    }
}
