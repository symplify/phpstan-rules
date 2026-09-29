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
}
