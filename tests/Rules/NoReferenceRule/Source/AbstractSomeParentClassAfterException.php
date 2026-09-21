<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\Source;

use Exception;

final class SomeExceptionBeforeParentClass extends Exception
{
}

abstract class AbstractSomeParentClassAfterException
{
    public function someMethod(&$useIt)
    {
    }
}
