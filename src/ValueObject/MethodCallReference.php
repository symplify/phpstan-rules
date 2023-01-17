<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class MethodCallReference
{
    public function __construct(
        private readonly string $class,
        private readonly string $method
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
