<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PHPStan\Reflection\ReflectionProvider;

/**
 * Detects a Laravel project, so Symfony-only rules can skip it.
 */
final readonly class LaravelPresenceResolver
{
    private const string LARAVEL_APPLICATION_CLASS = 'Illuminate\Foundation\Application';

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
    }

    public function isLaravelProject(): bool
    {
        return $this->reflectionProvider->hasClass(self::LARAVEL_APPLICATION_CLASS);
    }
}
