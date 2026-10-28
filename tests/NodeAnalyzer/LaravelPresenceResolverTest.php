<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\NodeAnalyzer;

use PHPStan\Testing\PHPStanTestCase;
use Symplify\PHPStanRules\NodeAnalyzer\LaravelPresenceResolver;

final class LaravelPresenceResolverTest extends PHPStanTestCase
{
    public function testNonLaravelProject(): void
    {
        $laravelPresenceResolver = new LaravelPresenceResolver($this->createReflectionProvider());

        // Laravel is not installed here, so it must not be detected
        $this->assertFalse($laravelPresenceResolver->isLaravelProject());
    }

    public function testRegisteredInSharedServices(): void
    {
        // rules picked one by one only include services.neon, so the resolver must be registered there
        $laravelPresenceResolver = self::getContainer()->getByType(LaravelPresenceResolver::class);

        $this->assertInstanceOf(LaravelPresenceResolver::class, $laravelPresenceResolver);
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../config/included_services.neon'];
    }
}
