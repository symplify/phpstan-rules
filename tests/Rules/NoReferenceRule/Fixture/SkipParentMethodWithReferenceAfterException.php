<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\Source\AbstractSomeParentClassAfterException;

final class SkipParentMethodWithReferenceAfterException extends AbstractSomeParentClassAfterException
{
    public function someMethod(&$useIt)
    {
    }
}
