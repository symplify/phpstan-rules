<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\ConfigClosure\NoAutoconfiguredServiceTagRule\Source;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SomeSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [];
    }
}
