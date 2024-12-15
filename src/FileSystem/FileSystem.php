<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\FileSystem;

use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class FileSystem
{
    public static function read(string $file): string
    {
        $fileContents = file_get_contents($file);
        if ($fileContents === false) {
            throw new ShouldNotHappenException(sprintf('File "%s" was not found.', $file));
        }

        return $fileContents;
    }
}
