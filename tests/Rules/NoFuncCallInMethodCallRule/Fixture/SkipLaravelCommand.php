<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule\Fixture;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SkipLaravelCommand extends Command
{
    public function handle()
    {
        $this->note(sprintf('This is "%s"', 'me'));
    }
}
