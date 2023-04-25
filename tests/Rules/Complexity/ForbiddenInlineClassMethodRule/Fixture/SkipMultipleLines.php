<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenInlineClassMethodRule\Fixture;

final class SkipMultipleLines
{
    private const EXAMPLE_CONST = 123;

    public function run()
    {
        return $this->away();
    }

    private function away()
    {
        return $this->complexFunction(
            self::EXAMPLE_CONST,
            ['foo' => 'bar', 'bar' => 'foo'],
            'example_string'
        );
    }

    private function complexFunction(int $foo, array $bar, string $foobar)
    {
        return null;
    }
}
