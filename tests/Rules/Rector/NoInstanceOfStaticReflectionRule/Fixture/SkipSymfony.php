<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Rector\NoInstanceOfStaticReflectionRule\Fixture;

use Symfony\Component\Console\Helper\ProgressBar;

final class SkipSymfony
{
    public function find(object $node): bool
    {
        return $node instanceof ProgressBar;
    }
}
