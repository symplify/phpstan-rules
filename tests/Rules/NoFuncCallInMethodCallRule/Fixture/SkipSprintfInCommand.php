<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule\Fixture;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SkipSprintfInCommand extends Command
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('This is "%s"', 'me'));
    }
}
