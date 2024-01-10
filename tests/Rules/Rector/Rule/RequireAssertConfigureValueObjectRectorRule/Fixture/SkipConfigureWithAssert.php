<?php

declare(strict_types=1);

namespace Rector\PHPStanRules\Tests\Rule\RequireAssertConfigureValueObjectRectorRule\Fixture;

use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PHPStanRules\Tests\Rule\RequireAssertConfigureValueObjectRectorRule\Source\SomeValueObject;
use Webmozart\Assert\Assert;

final class SkipConfigureWithAssert implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    private const SOME_KEY = 'some_key';

    /**
     * @param array<string, SomeValueObject[]> $configuration
     */
    public function configure(array $configuration): void
    {
        $valueObjects = $configuration[self::SOME_KEY] ?? [];
        Assert::allIsAOf($valueObjects, SomeValueObject::class);
    }
}
