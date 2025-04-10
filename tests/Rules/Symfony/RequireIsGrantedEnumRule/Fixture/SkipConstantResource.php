<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\RequireIsGrantedEnumRule\Fixture;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symplify\PHPStanRules\Tests\Rules\Symfony\RequireIsGrantedEnumRule\Source\SomePermission;

#[IsGranted(SomePermission::ACCESS)]
final class SkipConstantResource
{
    public function run()
    {
    }
}
