<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoRelativeFilePathRule\Fixture;

final class SkipUrls
{
    public function run()
    {
        $partialDomain = 'mearie\.org';

        $partialDomain = 'mearie.org';

        return 'https://someurl.com' ;
    }
}
