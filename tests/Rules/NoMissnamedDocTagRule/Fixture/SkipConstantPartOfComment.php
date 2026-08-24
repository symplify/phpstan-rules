<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMissnamedDocTagRule\Fixture;

final class SkipConstantPartOfComment
{
    /**
     * Using 10-level array @return docblocks makes code very hard to read,
     * lets limit it to reasonable level
     */
    private const int MAX_NESTING = 3;
}
