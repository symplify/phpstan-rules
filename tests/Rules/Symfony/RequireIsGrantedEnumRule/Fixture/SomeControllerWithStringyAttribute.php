<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireIsGrantedEnumRule\Fixture;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('some_resource')]
final class SomeControllerWithStringyAttribute
{
    public function run()
    {
    }
}
