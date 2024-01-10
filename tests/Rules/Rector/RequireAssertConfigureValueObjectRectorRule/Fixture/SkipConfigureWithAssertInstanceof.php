<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\RequireAssertConfigureValueObjectRectorRule\Fixture;

use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\PHPStanRules\Tests\Rules\Rector\RequireAssertConfigureValueObjectRectorRule\Source\SomeValueObject;
use Webmozart\Assert\Assert;

final class SkipConfigureWithAssertInstanceof implements ConfigurableRectorInterface
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
        Assert::allIsInstanceOf($valueObjects, SomeValueObject::class);
    }
}
