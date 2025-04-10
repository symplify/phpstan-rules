<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoBareAndSecurityIsGrantedContentsRule\Fixture;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('(has_role("some_resource") and is_granted("another_resource")) or is_granted("yet_another_resource")')]
final class SkipInnerOr
{
    public function run()
    {
    }
}
