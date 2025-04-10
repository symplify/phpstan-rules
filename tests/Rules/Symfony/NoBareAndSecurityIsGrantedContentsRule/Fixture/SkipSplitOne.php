<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoBareAndSecurityIsGrantedContentsRule\Fixture;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('some_resource')]
#[IsGranted('another_resource')]
final class SkipSplitOne
{
    public function run()
    {
    }
}
