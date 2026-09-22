<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireAttributeNameRule\Fixture;

use Symfony\Component\DependencyInjection\Attribute\When;

#[When('prod')]
final class SkipSingleParamAttribute
{
}
