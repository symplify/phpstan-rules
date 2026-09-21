<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConstraintMustHaveAttributeRule\Fixture;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class SkipConstraintWithAttribute extends Constraint
{
}
