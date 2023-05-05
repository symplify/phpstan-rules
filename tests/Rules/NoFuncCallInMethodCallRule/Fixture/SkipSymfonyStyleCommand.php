<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule\Fixture;

use Symfony\Component\Console\Output\OutputInterface;

final class SkipSymfonyStyleCommand
{
    public function execute(OutputInterface $output)
    {
        $output->writeln(sprintf('This is "%s"', 'me'));
    }
}
