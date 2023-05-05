<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule\Fixture;

use Illuminate\Console\Command;

final class SkipLaravelCommand extends Command
{
    public function handle()
    {
        $this->note(sprintf('This is "%s"', 'me'));
    }
}
