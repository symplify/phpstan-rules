<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\Fixture;

final class SkipUseInReference
{
    public function someMethod($filePath)
    {
        return preg_replace_callback(
            '#{(.*?)}#m',
            function (array $match) use (&$i, $arguments) {
            },
            $filePath
        );
    }
}
