<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Fixture;

use RuntimeException;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoNullableServiceInConstructorRule\Source\SomeService;

final class SkipExceptionClass extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly ?SomeService $someService = null,
    ) {
        parent::__construct($message);
    }
}
