<?php

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoRequiredOutsideClassRule\Fixture;

final class SomeClassUsingTrait
{
    use TraitWithRequire;
    use TraitWithRequireAttribute;
}
