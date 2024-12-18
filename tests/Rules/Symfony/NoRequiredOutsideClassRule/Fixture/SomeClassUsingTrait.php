<?php

namespace TomasVotruba\Handyman\Tests\PHPStan\Rule\NoRequiredOutsideClassRule\Fixture;

final class SomeClassUsingTrait
{
    use TraitWithRequire;
    use TraitWithRequireAttribute;
}
