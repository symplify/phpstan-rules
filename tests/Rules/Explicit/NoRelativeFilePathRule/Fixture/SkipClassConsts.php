<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoRelativeFilePathRule\Fixture;

final class SkipClassConsts
{
    /**
     * @var string[]
     */
    private const SUFFIXES = ['neon', 'yaml', 'xml', 'yml', 'twig', 'latte', 'blade.php', 'tpl'];
}
