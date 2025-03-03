<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\NoGetInCommandRule\Fixture;

use Symfony\Component\Console\Command\Command;
use Symplify\PHPStanRules\Tests\Rules\Symfony\NoGetInCommandRule\Source\SomeType;

final class SomeCommandWithGet extends Command
{
    public function run()
    {
        $this->get(SomeType::class);
    }
}
