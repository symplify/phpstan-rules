<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoDuplicateArgAutowireByTypeRule\Source;

final class SomeServiceWithConstructor
{
    public function __construct(AnotherType $anotherType)
    {
    }
}
