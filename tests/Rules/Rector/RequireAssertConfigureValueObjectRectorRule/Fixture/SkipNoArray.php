<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\RequireAssertConfigureValueObjectRectorRule\Fixture;

use Rector\Contract\Rector\ConfigurableRectorInterface;

final class SkipNoArray implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    private const SOME_KEY = 'some_key';

    /**
     * @param array<string, int> $configuration
     */
    public function configure(array $configuration): void
    {
        $valueObjects = $configuration[self::SOME_KEY] ?? [];
    }
}
