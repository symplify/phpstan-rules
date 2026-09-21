<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Symfony\CommandMustHaveAsCommandAttributeRule\Fixture;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand('app:report')]
final class SkipCommandWithAttribute extends Command
{
}
