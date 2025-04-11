<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\PreferAutowireAttributeOverConfigParamRule\Source;

final class SomeSetServiceWithConstructor
{
    public function __construct(string $key)
    {
    }
}
